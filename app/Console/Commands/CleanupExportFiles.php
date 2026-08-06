<?php

namespace App\Console\Commands;

use App\Models\ExportLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exports:cleanup {--hours=24 : Hapus file export lebih tua dari N jam}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus file fisik export lama beserta log-nya agar penyimpanan tidak menumpuk';

    /**
     * Execute the console command.
     *
     * Logika:
     * - Hapus file fisik yang sudah lebih tua dari --hours jam
     * - Sebelum hapus file, hapus juga catatan ExportLog di database yang referensikan file itu
     * - Dengan begitu, user tidak akan melihat tombol download untuk file yang sudah tidak ada
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        if ($hours <= 0) {
            $this->error('Nilai --hours harus lebih dari 0.');
            return self::FAILURE;
        }

        $disk = Storage::disk('local');
        $baseDir = 'exports';

        if (!$disk->exists($baseDir)) {
            $this->info('Folder exports belum ada. Tidak ada file yang dibersihkan.');
            return self::SUCCESS;
        }

        $cutoffTimestamp = now()->subHours($hours)->timestamp;
        $deletedCount = 0;
        $logsDeletedCount = 0;
        $scannedCount = 0;

        foreach ($disk->allFiles($baseDir) as $path) {
            $scannedCount++;

            try {
                $lastModified = $disk->lastModified($path);
            } catch (\Throwable $e) {
                $this->warn("Lewati file (gagal baca metadata): {$path}");
                continue;
            }

            if ($lastModified <= $cutoffTimestamp) {
                // Hapus catatan ExportLog yang referensikan file ini terlebih dahulu
                $filename = basename($path);
                $deletedLogs = ExportLog::where('nama_file', $filename)->delete();
                $logsDeletedCount += $deletedLogs;

                // Baru hapus file fisiknya
                if ($disk->delete($path)) {
                    $deletedCount++;
                }
            }
        }

        $this->info("Selesai cleanup export: scan={$scannedCount}, files_deleted={$deletedCount}, logs_deleted={$logsDeletedCount}, retention={$hours} jam.");

        return self::SUCCESS;
    }
}
