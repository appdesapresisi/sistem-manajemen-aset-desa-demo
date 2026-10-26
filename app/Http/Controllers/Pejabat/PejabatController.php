<?php

namespace App\Http\Controllers\Pejabat;

use App\Http\Controllers\Controller;
use App\Services\PejabatService;
use App\Services\ActivityLogger;
use App\Http\Requests\StorePejabatRequest;
use App\Http\Requests\UpdatePejabatRequest;
use App\Models\Pejabat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class PejabatController extends Controller
{
    protected $service;

    public function __construct(PejabatService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Pejabat::class);
        
        // Build query with filters
        $query = Pejabat::query();
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('jabatan', 'like', '%' . $search . '%')
                  ->orWhere('tugas', 'like', '%' . $search . '%');
            });
        }
        
        // Jabatan filter
        if ($request->has('jabatan') && is_array($request->jabatan) && count($request->jabatan) > 0) {
            $jabatanList = $request->jabatan;
            $query->where(function($q) use ($jabatanList) {
                foreach ($jabatanList as $jab) {
                    if ($jab === 'Perbekel') {
                        $q->orWhere('jabatan', 'like', '%perbekel%')
                          ->orWhere('jabatan', 'like', '%kepala desa%');
                    } elseif ($jab === 'Sekretaris Desa') {
                        $q->orWhere('jabatan', 'like', '%sekretaris%');
                    } elseif ($jab === 'Kaur' || $jab === 'Kepala Urusan') {
                        $q->orWhere('jabatan', 'like', '%kaur%');
                    } elseif ($jab === 'Kasi' || $jab === 'Kepala Seksi') {
                        $q->orWhere('jabatan', 'like', '%kasi%');
                    } elseif ($jab === 'Kelian Banjar') {
                        $q->orWhere('jabatan', 'like', '%kelian%');
                    } elseif ($jab === 'Staf') {
                        $q->orWhere('jabatan', 'like', '%staf%');
                    } else {
                        $q->orWhere('jabatan', 'like', '%' . $jab . '%');
                    }
                }
            });
        }

        // Sort order
        $sortBy = $request->get('sort', 'terbaru');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } elseif ($sortBy === 'nama_asc') {
            $query->orderBy('nama', 'asc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            // terbaru (default)
            $query->orderBy('created_at', 'desc');
        }
        
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }
        $pejabatsPaginated = $query->withCount('users')->paginate($perPage)->appends($request->except('page'));
        
        // Get total counts from all pejabats (not just current page)
        $statsRow = Pejabat::query()
            ->selectRaw('COUNT(*) as total_pejabat')
            ->selectRaw("SUM(CASE WHEN ttd_path IS NOT NULL AND ttd_path <> '' THEN 1 ELSE 0 END) as ttd_lengkap")
            ->selectRaw("COUNT(DISTINCT CASE WHEN jabatan IS NOT NULL AND jabatan <> '' THEN jabatan END) as total_jabatan")
            ->first();

        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $pejabatToday = Pejabat::where('created_at', '>=', $todayStart)->count();
        $pejabatYesterday = Pejabat::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $pejabatDiff = $pejabatToday - $pejabatYesterday;
        $pejabatPercent = $pejabatYesterday > 0 ? round(($pejabatDiff / $pejabatYesterday) * 100, 1) : ($pejabatToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $calcMonthlyTrend = function($queryCallback) use ($thisMonthStart, $lastMonthStart) {
            $thisMonth = $queryCallback(Pejabat::query()->where('created_at', '>=', $thisMonthStart))->count();
            $lastMonth = $queryCallback(Pejabat::query()->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart))->count();
            $diff = $thisMonth - $lastMonth;
            $percent = $lastMonth > 0 ? round(($diff / $lastMonth) * 100, 1) : ($thisMonth > 0 ? 100 : 0);
            return ['diff' => $diff, 'percent' => $percent];
        };

        $trends = [
            'total'     => $calcMonthlyTrend(fn($q) => $q),
            'ttd'       => $calcMonthlyTrend(fn($q) => $q->whereNotNull('ttd_path')->where('ttd_path', '!=', '')),
            'jabatan'   => $calcMonthlyTrend(fn($q) => $q->whereNotNull('jabatan')->where('jabatan', '!=', '')),
        ];

        $stats = [
            'totalPejabat' => (int) ($statsRow->total_pejabat ?? 0),
            'ttdLengkap'   => (int) ($statsRow->ttd_lengkap ?? 0),
            'totalJabatan' => (int) ($statsRow->total_jabatan ?? 0),
            'trends'       => $trends,
        ];
        
        // Avatar colors for cycling
        $avatarColors = [
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
            'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
            'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
        ];
        
        // Transform paginated pejabats with computed properties (manual loop)
        foreach ($pejabatsPaginated as $index => $pejabat) {
            // Generate initials
            $nama = $pejabat->nama;
            $words = explode(' ', $nama);
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($nama, 0, 2));
            }
            
            // Determine badge class based on jabatan
            $jabatan = strtolower($pejabat->jabatan);
            if (str_contains($jabatan, 'kepala desa') || str_contains($jabatan, 'perbekel')) {
                $badgeClass = 'perbekel';
                $badgeIcon = 'award';
            } elseif (str_contains($jabatan, 'sekretaris')) {
                $badgeClass = 'sekdes';
                $badgeIcon = 'clipboard';
            } else {
                $badgeClass = 'kaur';
                $badgeIcon = 'briefcase';
            }
            
            $pejabat->initials = $initials;
            $pejabat->avatar_color = $avatarColors[$index % count($avatarColors)];
            $pejabat->badge_class = $badgeClass;
            $pejabat->badge_icon = $badgeIcon;
        }
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'pejabats' => $pejabatsPaginated->items(),
                'stats' => $stats,
                'pagination' => [
                    'firstItem' => $pejabatsPaginated->firstItem(),
                    'lastItem' => $pejabatsPaginated->lastItem(),
                    'currentPage' => $pejabatsPaginated->currentPage(),
                    'lastPage' => $pejabatsPaginated->lastPage(),
                    'perPage' => $pejabatsPaginated->perPage(),
                    'total' => $pejabatsPaginated->total(),
                ],
            ]);
        }
        
        return view('pejabat.index', [
            'pejabats' => $pejabatsPaginated,
            'stats' => $stats,
        ]);
    }

    /**
     * Download template resmi CSV (.csv) untuk import data pejabat aparatur desa.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Pejabat::class);
        $fileName = 'Template_Import_Pejabat_' . date('Ymd_His') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PejabatTemplateExport(), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Proses import data pejabat dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function import(Request $request)
    {
        $this->authorize('create', Pejabat::class);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih file CSV (.csv) untuk diimpor.',
            'file_excel.file'     => 'File yang diunggah tidak valid.',
            'file_excel.mimes'    => 'Format file harus berupa CSV (.csv). Format Excel (.xlsx/.xls) tidak didukung.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $importer = new \App\Imports\PejabatImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file_excel'));

            // Log aktivitas
            ActivityLogger::log(
                'Pejabat',
                'tambah',
                "Import CSV: {$importer->importedCount} data berhasil diimpor, {$importer->skippedCount} dilewati (duplikat), {$importer->failedCount} gagal."
            );

            $resultData = [
                'success' => true,
                'message' => "Proses impor selesai. {$importer->importedCount} pejabat berhasil ditambahkan ke sistem.",
                'stats' => [
                    'total_rows'     => $importer->totalRows,
                    'imported_count' => $importer->importedCount,
                    'skipped_count'  => $importer->skippedCount,
                    'failed_count'   => $importer->failedCount,
                ],
                'imported_rows' => $importer->importedRows,
                'skipped_rows'  => $importer->skippedRows,
                'failed_rows'   => $importer->failedRows,
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($resultData);
            }

            return redirect()->route('pejabat.index')
                ->with('import_result', $resultData)
                ->with('success', "Proses impor selesai: {$importer->importedCount} pejabat berhasil diimpor.");

        } catch (\Throwable $e) {
            Log::error('Import Pejabat error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = $e instanceof \InvalidArgumentException
                ? $e->getMessage()
                : 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    public function create()
    {
        $this->authorize('create', Pejabat::class);
        return view('pejabat.create');
    }

    public function store(StorePejabatRequest $request)
    {
        $this->authorize('create', Pejabat::class);
        $data = $request->validated();
        
        // Handle file upload
        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
            $data['ttd_type'] = $request->input('ttd_type', 'gambar');
        }

        try {
            $pejabat = $this->service->create($data);
            ActivityLogger::log('Pejabat', 'tambah', 'Menambahkan pejabat ' . ($data['nama'] ?? '-'), $pejabat?->id, $data['nama'] ?? null);
            return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil ditambahkan ke sistem!');
        } catch (\Exception $e) {
            Log::error('PejabatController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pejabat. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $pejabat = $this->service->getById($id);
        if (!$pejabat) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('view', $pejabat);
        return view('pejabat.show', compact('pejabat'));
    }

    public function viewTtd($id)
    {
        $pejabat = $this->service->getById($id);
        if (!$pejabat || !$pejabat->ttd_path) {
            abort(404);
        }

        $this->authorize('view', $pejabat);

        // Use 'public' disk since TTD files are stored in storage/app/public/ttd
        $disk = Storage::disk('public');
        if (!$disk->exists($pejabat->ttd_path)) {
            abort(404);
        }

        // Get absolute path using storage_path
        $absolutePath = storage_path('app/public/' . ltrim($pejabat->ttd_path, '/'));
        if (!is_file($absolutePath)) {
            abort(404);
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function edit($id)
    {
        $p = $this->service->getById($id);
        if (!$p) return redirect()->route('pejabat.index')->withErrors('Pejabat tidak ditemukan');
        $this->authorize('update', $p);
        return view('pejabat.edit', ['pejabat' => $p]);
    }

    public function update(UpdatePejabatRequest $request, $id)
    {
        $data = $request->validated();

        // Status aktif/nonaktif pejabat hanya boleh diubah oleh super admin.
        if (!Gate::allows('create', Pejabat::class)) {
            unset($data['is_aktif']);
        }

        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
            $data['ttd_type'] = $request->input('ttd_type', 'gambar');
        }

        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('update', $existing);

        try {
            $this->service->update($id, $data);
            ActivityLogger::log('Pejabat', 'ubah', 'Mengubah data pejabat ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('PejabatController::update error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pejabat. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('delete', $existing);

        try {
            $result = $this->service->delete($id);
            ActivityLogger::log('Pejabat', 'hapus', 'Menghapus pejabat ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            $msg = 'Data pejabat berhasil dihapus dari sistem!';
            if (is_array($result) && ($result['unlinked_users'] ?? 0) > 0) {
                $msg .= ' ' . $result['unlinked_users'] . ' user telah dilepas dari pejabat ini.';
            }
            return redirect()->route('pejabat.index')->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('PejabatController::destroy error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage() ?: 'Gagal menghapus pejabat. Silakan coba lagi.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        // Authorize sekali di awal — policy delete Pejabat hanya untuk super_admin.
        // Tidak perlu per-item karena tidak ada ownership (bukan "hapus milik sendiri").
        $this->authorize('delete', Pejabat::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }
        $totalRequested = count($ids);
        $ids = array_slice($ids, 0, 100);

        $deleted = 0;
        $skipped = [];

        foreach ($ids as $id) {
            $pejabat = $this->service->getById($id);
            if (!$pejabat) continue;

            try {
                $this->service->delete($id);
                ActivityLogger::log('Pejabat', 'hapus', 'Menghapus pejabat ' . ($pejabat->nama ?? '-'), (int) $id, $pejabat->nama ?? null);
                $deleted++;
            } catch (\Throwable $e) {
                $skipped[] = ($pejabat->nama ?: ('ID #' . $id));
            }
        }

        $msg = $deleted . ' pejabat berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Dilewati (' . count($skipped) . ' pejabat masih digunakan sebagai penandatangan default jenis inventaris: ' . implode(', ', $skipped) . ').';
        }
        if ($totalRequested > 100) {
            $msg .= ' ' . ($totalRequested - 100) . ' data tersisa, pilih ulang untuk menghapus selanjutnya.';
        }

        $type = $deleted > 0 ? 'success' : 'error';
        return redirect()->route('pejabat.index')->with($type, $msg);
    }

    /**
     * Ekspor daftar pejabat desa ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Pejabat::class);
        $format = strtolower($request->query('format', 'excel'));

        $query = Pejabat::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('tugas', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['0', '1', 0, 1])) {
            $query->where('is_aktif', (int)$request->status);
        }

        $pejabats = $query->orderBy('nama', 'asc')->get();
        $dateStr = date('Ymd_His');

        ActivityLogger::log('Pejabat', 'export', 'Mengekspor daftar pejabat (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $headers = [
                ['text' => 'NO', 'width' => '4%', 'align' => 'center'],
                ['text' => 'NAMA PEJABAT', 'width' => '18%', 'align' => 'left'],
                ['text' => 'JABATAN', 'width' => '15%', 'align' => 'left'],
                ['text' => 'TUGAS & WEWENANG', 'width' => '18%', 'align' => 'left'],
                ['text' => 'ALAMAT', 'width' => '14%', 'align' => 'left'],
                ['text' => 'PENGURUS BARANG', 'width' => '14%', 'align' => 'center'],
                ['text' => 'TTD DIGITAL', 'width' => '10%', 'align' => 'center'],
                ['text' => 'STATUS', 'width' => '7%', 'align' => 'center'],
            ];

            $rows = [];
            foreach ($pejabats as $i => $item) {
                $statusText = ($item->is_aktif ?? 1) == 1 ? 'Aktif' : 'Nonaktif';
                $isPetugas  = $item->is_petugas_barang ? 'Ya' : 'Tidak';
                $ttdText    = $item->ttd_path ? 'Tersedia' : 'Belum Ada';

                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $item->nama ?? '-'],
                    ['text' => $item->jabatan ?? '-'],
                    ['text' => $item->tugas ?: '-'],
                    ['text' => $item->alamat ?: '-'],
                    ['text' => $isPetugas, 'class' => 'text-center'],
                    ['text' => $ttdText, 'class' => 'text-center'],
                    ['text' => $statusText, 'class' => 'text-center'],
                ];
            }

            $summary = [
                'Total Pejabat' => count($pejabats) . ' Orang',
                'Status Aktif'  => $pejabats->where('is_aktif', 1)->count() . ' Orang',
            ];

            return \App\Services\FeatureExportService::downloadPdf(
                'Daftar Pejabat Pengelola Aset Desa',
                'Struktur Pejabat dan Pengelola Barang Milik Desa',
                $headers,
                $rows,
                "Daftar_Pejabat_{$dateStr}.pdf",
                'portrait',
                $summary
            );
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PejabatExport($pejabats),
            "Daftar_Pejabat_{$dateStr}.xlsx"
        );
    }
}


