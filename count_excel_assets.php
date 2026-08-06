<?php
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = __DIR__ . '/LAPORAN HASIL INVENTARISASI ASET DESA MENGWI TAHUN 2025.xlsx';

if (!file_exists($filePath)) {
    die("File not found\n");
}

$reader = IOFactory::createReaderForFile($filePath);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($filePath);

$sheetNames = $spreadsheet->getSheetNames();
$grandTotal = 0;

foreach ($sheetNames as $index => $name) {
    if ($index === 0) {
        // Skip sheet #0 if it is just a summary sheet or different format
        continue;
    }
    
    $sheet = $spreadsheet->getSheet($index);
    $highestRow = $sheet->getHighestRow();
    
    $count = 0;
    $rowsSample = [];
    
    // Find the header row to know which column is "No" (typically A)
    // and count how many rows have a valid number in Column A
    for ($row = 1; $row <= $highestRow; $row++) {
        $cellA = $sheet->getCell('A' . $row)->getValue();
        if ($cellA !== null && is_numeric(trim($cellA))) {
            $count++;
            $grandTotal++;
            if ($count <= 3) {
                $rowsSample[] = [
                    'no' => trim($cellA),
                    'nama' => trim($sheet->getCell('B' . $row)->getValue() ?? ''),
                    'kode' => trim($sheet->getCell('C' . $row)->getValue() ?? ''),
                    'nup' => trim($sheet->getCell('D' . $row)->getValue() ?? ''),
                    'tahun' => trim($sheet->getCell('F' . $row)->getValue() ?? '')
                ];
            }
        }
    }
    
    echo "Sheet: '$name' has $count assets.\n";
    foreach ($rowsSample as $sample) {
        echo "  - No {$sample['no']}: {$sample['nama']} ({$sample['kode']}) NUP: {$sample['nup']}, Tahun: {$sample['tahun']}\n";
    }
    echo "\n";
}

echo "Grand Total Assets (Numeric No column): $grandTotal\n";
