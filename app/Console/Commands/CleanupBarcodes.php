<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventaris;
use Illuminate\Support\Facades\File;

class CleanupBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-barcodes {--dry-run : Tampilkan daftar file yang akan dihapus tanpa melakukan penghapusan fisik}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan file QR/barcode PNG di public/barcodes yang tidak terhubung dengan data aset aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dir = public_path('barcodes');

        if (!File::isDirectory($dir)) {
            $this->error("Direktori barcodes tidak ditemukan di: {$dir}");
            return 1;
        }

        $files = File::files($dir);
        $totalFiles = count($files);

        $this->info("Memindai {$totalFiles} file di direktori public/barcodes...");

        $deletedCount = 0;
        $keptCount = 0;
        $skippedCount = 0;
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->comment("=== MODE DRY-RUN: Tidak ada file yang benar-benar akan dihapus ===");
        }

        foreach ($files as $file) {
            $filename = $file->getFilename();

            // Format nama file: barcode_{id}.png
            if (preg_match('/^barcode_(\d+)\.png$/', $filename, $matches)) {
                $id = (int) $matches[1];

                // Cek apakah aset dengan ID tersebut ada di DB
                $exists = Inventaris::where('id', $id)->exists();

                if (!$exists) {
                    if ($dryRun) {
                        $this->line("Akan dihapus: {$filename} (Aset ID {$id} tidak ada di database)");
                    } else {
                        File::delete($file->getRealPath());
                        $this->line("Dihapus: {$filename} (Aset ID {$id} tidak ada di database)");
                    }
                    $deletedCount++;
                } else {
                    $keptCount++;
                }
            } else {
                // File lain yang tidak cocok dengan format regex barcode
                $this->warn("Dilewati (format tidak sesuai): {$filename}");
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("=== HASIL PEMBERSIHAN ===");
        $this->info("Total file dipindai: {$totalFiles}");
        $this->info("File " . ($dryRun ? "yang akan dihapus" : "berhasil dihapus") . ": {$deletedCount}");
        $this->info("File dipertahankan (aktif): {$keptCount}");
        $this->info("File dilewati: {$skippedCount}");

        return 0;
    }
}
