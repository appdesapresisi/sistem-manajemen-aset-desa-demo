<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventaris;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateMissingBarcodes extends Command
{
    protected $signature = 'app:generate-missing-barcodes
                            {--force : Regenerate juga QR PNG untuk aset yang sudah punya barcode tapi file PNG-nya hilang}';

    protected $description = 'Generate barcode string dan QR PNG untuk aset yang belum memiliki barcode (biasanya aset dari seeder)';

    public function handle()
    {
        if (!Schema::hasColumn('inventaris', 'barcode')) {
            $this->error('Kolom barcode tidak ditemukan di tabel inventaris.');
            return 1;
        }

        $dir = public_path('barcodes');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Ambil aset yang belum punya barcode string
        $missing = Inventaris::whereNull('barcode')->orWhere('barcode', '')->get();
        $this->info("Ditemukan {$missing->count()} aset tanpa barcode.");

        if ($missing->count() === 0) {
            $this->line('Semua aset sudah memiliki barcode.');
        } else {
            $bar = $this->output->createProgressBar($missing->count());
            $bar->start();

            $success = 0;
            $fail    = 0;

            foreach ($missing as $item) {
                try {
                    // Generate unique barcode string
                    $barcode = $this->generateUniqueBarcode();

                    // Simpan ke DB dulu
                    $item->barcode = $barcode;
                    $item->save();

                    // Buat QR PNG
                    $this->writeQrPng($item->id, $barcode, $dir);

                    $success++;
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->warn("Gagal untuk ID {$item->id}: " . $e->getMessage());
                    $fail++;
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Selesai: {$success} berhasil, {$fail} gagal.");
        }

        // Jika --force: regenerasi juga PNG yang filenya hilang
        if ($this->option('force')) {
            $this->newLine();
            $this->info('Mode --force: memeriksa aset yang sudah punya barcode tapi file PNG hilang...');

            $withBarcode = Inventaris::whereNotNull('barcode')->where('barcode', '!=', '')->get();
            $regenerated = 0;

            foreach ($withBarcode as $item) {
                $pngPath = $dir . DIRECTORY_SEPARATOR . "barcode_{$item->id}.png";
                if (!file_exists($pngPath)) {
                    try {
                        $this->writeQrPng($item->id, $item->barcode, $dir);
                        $regenerated++;
                    } catch (\Exception $e) {
                        $this->warn("Gagal regenerasi PNG untuk ID {$item->id}: " . $e->getMessage());
                    }
                }
            }

            $this->info("Regenerasi PNG yang hilang: {$regenerated} file dibuat ulang.");
        }

        return 0;
    }

    private function generateUniqueBarcode(): string
    {
        $attempt = 0;
        $exists  = false;
        do {
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));
            $exists    = Inventaris::where('barcode', $candidate)->exists();
            $attempt++;
        } while ($exists && $attempt < 10);

        if ($exists) {
            // Fallback UUID-based agar dijamin unik
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
            if (Inventaris::where('barcode', $candidate)->exists()) {
                $candidate = 'INV-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
            }
        }

        return $candidate;
    }

    private function writeQrPng(int $inventarisId, string $barcode, string $dir): void
    {
        $qrData = URL::signedRoute('inventaris.scan', ['barcode' => $barcode]);

        if (!preg_match('#^https?://#i', $qrData)) {
            $qrData = 'http://' . ltrim($qrData, '/');
        }

        $qrCode = QrCode::create($qrData)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $result->saveToFile($dir . DIRECTORY_SEPARATOR . "barcode_{$inventarisId}.png");
    }
}
