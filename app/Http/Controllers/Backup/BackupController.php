<?php

namespace App\Http\Controllers\Backup;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    /**
     * Tampilkan halaman manajemen cadangan basis data.
     */
    public function index(Request $request)
    {
        $this->authorizeSuperDeveloper();

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Ambil daftar file backup
        $allFiles = [];
        $rawFiles = scandir($backupDir);
        foreach ($rawFiles as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.sql')) {
                continue;
            }
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($filePath)) {
                continue;
            }

            $sizeBytes = filesize($filePath) ?: 0;
            $mtime = filemtime($filePath);
            $createdAt = Carbon::createFromTimestamp($mtime);

            $allFiles[] = [
                'filename'       => $file,
                'size_bytes'     => $sizeBytes,
                'size_formatted' => $this->formatBytes($sizeBytes),
                'created_at'     => $createdAt,
                'date_formatted' => $createdAt->translatedFormat('d F Y, H:i:s'),
                'age_human'      => $createdAt->diffForHumans(),
            ];
        }

        // Urutkan default terbaru di atas untuk kalkulasi stats
        usort($allFiles, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        // Metadata basis data
        $dbName = DB::connection()->getDatabaseName();
        $tables = [];
        try {
            $rawTables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            foreach ($rawTables as $t) {
                $val = array_values((array) $t);
                $tables[] = $val[0];
            }
        } catch (\Throwable $e) {
            Log::warning('BackupController: gagal mengambil daftar tabel: ' . $e->getMessage());
        }

        $dbSizeBytes = 0;
        try {
            $sizeRow = DB::selectOne("
                SELECT SUM(data_length + index_length) AS size_bytes
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$dbName]);
            $dbSizeBytes = (int) ($sizeRow->size_bytes ?? 0);
        } catch (\Throwable $e) {
            Log::warning('BackupController: gagal menghitung ukuran database: ' . $e->getMessage());
        }

        $stats = [
            'total_backups'     => count($allFiles),
            'db_name'           => $dbName,
            'total_tables'      => count($tables),
            'db_size_formatted' => $this->formatBytes($dbSizeBytes),
            'latest_backup'     => !empty($allFiles) ? $allFiles[0]['date_formatted'] : 'Belum pernah',
        ];

        // Filter pencarian nama berkas
        $filteredFiles = $allFiles;
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $filteredFiles = array_filter($filteredFiles, function ($f) use ($search) {
                return stripos($f['filename'], $search) !== false;
            });
            $filteredFiles = array_values($filteredFiles);
        }

        // Pengurutan
        $sort = (string) $request->input('sort', 'terbaru');
        if ($sort === 'terlama') {
            usort($filteredFiles, fn($a, $b) => $a['created_at']->timestamp <=> $b['created_at']->timestamp);
        } elseif ($sort === 'nama_asc') {
            usort($filteredFiles, fn($a, $b) => strcasecmp($a['filename'], $b['filename']));
        } elseif ($sort === 'nama_desc') {
            usort($filteredFiles, fn($a, $b) => strcasecmp($b['filename'], $a['filename']));
        } elseif ($sort === 'ukuran_desc') {
            usort($filteredFiles, fn($a, $b) => $b['size_bytes'] <=> $a['size_bytes']);
        } elseif ($sort === 'ukuran_asc') {
            usort($filteredFiles, fn($a, $b) => $a['size_bytes'] <=> $b['size_bytes']);
        } else {
            // terbaru (default)
            usort($filteredFiles, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);
        }

        // Paginasi standar (10, 20, 50, 100)
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }
        $currentPage = (int) $request->input('page', 1);
        if ($currentPage < 1) $currentPage = 1;

        $totalItems = count($filteredFiles);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = array_slice($filteredFiles, $offset, $perPage);

        $files = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $totalItems,
            $perPage,
            $currentPage,
            [
                'path'  => route('backup.index'),
                'query' => $request->query(),
            ]
        );

        return view('backup.index', compact('files', 'stats'));
    }

    /**
     * Buat cadangan basis data SQL baru.
     */
    public function store(Request $request)
    {
        $this->authorizeSuperDeveloper();

        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_sima_' . date('Y_m_d_His') . '.sql';
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $handle = fopen($filePath, 'w+');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuat berkas cadangan pada direktori penyimpanan.');
        }

        try {
            $pdo = DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            $desaName = config('desa.nama_lokasi') ?? 'Desa Demo';
            $waktuCetak = now()->translatedFormat('d F Y, H:i:s');

            // Header SQL
            fwrite($handle, "-- ============================================================\n");
            fwrite($handle, "-- SISTEM MANAJEMEN ASET DESA (SIMA) - CADANGAN BASIS DATA\n");
            fwrite($handle, "-- Wilayah: {$desaName}\n");
            fwrite($handle, "-- Basis Data: {$dbName}\n");
            fwrite($handle, "-- Waktu Pembuatan: {$waktuCetak}\n");
            fwrite($handle, "-- Oleh: " . auth()->user()->nama . " (" . auth()->user()->username . ")\n");
            fwrite($handle, "-- ============================================================\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET time_zone = \"+00:00\";\n");
            fwrite($handle, "SET NAMES utf8mb4;\n\n");

            // Ambil seluruh tabel dasar (hanya BASE TABLE, hindari VIEW)
            $tables = [];
            $rawTables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            foreach ($rawTables as $t) {
                $val = array_values((array) $t);
                $tables[] = $val[0];
            }

            foreach ($tables as $table) {
                // Struktur Tabel
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "-- Struktur tabel `{$table}`\n");
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

                $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
                if (!empty($createRow)) {
                    $createArray = (array) $createRow[0];
                    $createSql = $createArray['Create Table'] ?? array_values($createArray)[1];
                    fwrite($handle, $createSql . ";\n\n");
                }

                // Data Isi Tabel
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "-- Data tabel `{$table}`\n");
                fwrite($handle, "-- ------------------------------------------------------------\n");

                $chunk = [];
                $cursor = DB::table($table)->cursor();
                foreach ($cursor as $row) {
                    $vals = [];
                    foreach ((array) $row as $colVal) {
                        if (is_null($colVal)) {
                            $vals[] = 'NULL';
                        } elseif (is_int($colVal) || is_float($colVal)) {
                            $vals[] = $colVal;
                        } else {
                            $vals[] = $pdo->quote((string) $colVal);
                        }
                    }
                    $chunk[] = '(' . implode(', ', $vals) . ')';

                    if (count($chunk) >= 100) {
                        fwrite($handle, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $chunk) . ";\n");
                        $chunk = [];
                    }
                }

                if (!empty($chunk)) {
                    fwrite($handle, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $chunk) . ";\n\n");
                } else {
                    fwrite($handle, "-- (tabel kosong)\n\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "-- ============================================================\n");
            fwrite($handle, "-- Akhir dari berkas cadangan SIMA.\n");
            fwrite($handle, "-- ============================================================\n");

            fclose($handle);

            $fileSize = filesize($filePath) ?: 0;
            $formattedSize = $this->formatBytes($fileSize);

            ActivityLogger::log('Backup', 'tambah', "Membuat cadangan basis data {$filename} ({$formattedSize})");

            return redirect()->route('backup.index')->with('success', "Cadangan basis data berhasil dibuat: {$filename} ({$formattedSize})");
        } catch (\Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            Log::error('BackupController::store failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses cadangan basis data: ' . $e->getMessage());
        }
    }

    /**
     * Unduh berkas cadangan SQL.
     */
    public function download(string $filename)
    {
        $this->authorizeSuperDeveloper();

        // Sanitasi nama berkas (cegah Directory Traversal)
        if (!preg_match('/^backup_sima_[0-9_]+\.sql$/', $filename)) {
            abort(404, 'Berkas cadangan tidak valid.');
        }

        $filePath = storage_path('app/backups' . DIRECTORY_SEPARATOR . $filename);
        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'Berkas cadangan tidak ditemukan.');
        }

        ActivityLogger::log('Backup', 'export', "Mengunduh berkas cadangan basis data {$filename}");

        return response()->download($filePath, $filename, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Hapus berkas cadangan SQL.
     */
    public function destroy(string $filename)
    {
        $this->authorizeSuperDeveloper();

        if (!preg_match('/^backup_sima_[0-9_]+\.sql$/', $filename)) {
            abort(404, 'Berkas cadangan tidak valid.');
        }

        $filePath = storage_path('app/backups' . DIRECTORY_SEPARATOR . $filename);
        if (!file_exists($filePath) || !is_file($filePath)) {
            return redirect()->route('backup.index')->with('error', 'Berkas cadangan tidak ditemukan.');
        }

        @unlink($filePath);

        ActivityLogger::log('Backup', 'hapus', "Menghapus berkas cadangan basis data {$filename}");

        return redirect()->route('backup.index')->with('success', "Berkas cadangan {$filename} berhasil dihapus.");
    }

    /**
     * Otorisasi khusus: Hanya Super Developer / Platform Owner yang berhak mengakses cadangan basis data.
     */
    protected function authorizeSuperDeveloper(): void
    {
        if (!auth()->check() || !auth()->user()->isSuperDeveloper()) {
            abort(403, 'Akses terbatas untuk Super Developer / Platform Owner.');
        }
    }

    /**
     * Helper pemformat ukuran bytes ke format yang mudah dibaca.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}

