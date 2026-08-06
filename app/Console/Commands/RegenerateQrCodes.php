<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventaris;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\URL;

class RegenerateQrCodes extends Command
{
    protected $signature = 'app:regenerate-qr-codes';
    protected $description = 'Regenerate all QR code PNG files to embed scan URL instead of plain barcode string';

    public function handle()
    {
        $dir = public_path('barcodes');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $inventaris = Inventaris::whereNotNull('barcode')->get();
        $this->info("Found {$inventaris->count()} inventaris with barcode.");

        $bar = $this->output->createProgressBar($inventaris->count());
        $bar->start();

        foreach ($inventaris as $item) {
            try {
                $qrData = URL::signedRoute('inventaris.scan', ['barcode' => $item->barcode]);
                // Ensure scheme is present so some scanner apps don't produce schemeless text
                if (!preg_match('#^https?://#i', $qrData)) {
                    $qrData = 'http://' . ltrim($qrData, '/');
                }
                $filename = "barcode_{$item->id}.png";
                $path     = $dir . DIRECTORY_SEPARATOR . $filename;

                // DEBUG: print the QR payload we write into the PNG (raw JSON to reveal control chars)
                $this->line('QR payload raw for ID ' . $item->id . ': ' . json_encode($qrData));
                $hasNl = strpos($qrData, "\n") !== false ? 'yes' : 'no';
                $this->line('Contains newline char? ' . $hasNl . ' | length=' . strlen($qrData));

                $qrCode = QrCode::create($qrData)
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $result->saveToFile($path);
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Failed for ID {$item->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done. All QR codes now embed the scan URL.');
    }
}
