<?php
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFile = 'LAPORAN HASIL INVENTARISASI ASET DESA MENGWI TAHUN 2025.xlsx';

if (!file_exists($excelFile)) {
    die("File not found\n");
}

$reader = IOFactory::createReaderForFile($excelFile);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($excelFile);
$sheetNames = $spreadsheet->getSheetNames();

foreach ($sheetNames as $idx => $name) {
    if ($idx === 0) continue; // Skip summary sheet
    $sheet = $spreadsheet->getSheet($idx);
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    
    // Find the header row (has 'No' or 'Nama Barang' or 'Jenis')
    $headerRowIndex = 0;
    $headers = [];
    $sampleRow = [];
    
    for ($row = 1; $row <= 25; $row++) {
        $valA = trim($sheet->getCell('A' . $row)->getValue() ?? '');
        $valB = trim($sheet->getCell('B' . $row)->getValue() ?? '');
        if (strcasecmp($valA, 'No') === 0 || strcasecmp($valB, 'Nama Barang') === 0 || strcasecmp($valB, 'Jenis Bangunan') === 0 || strcasecmp($valB, 'Jenis Tanah') === 0 || strcasecmp($valB, 'Jenis Jalan Irigasi dan Jaringan') === 0) {
            $headerRowIndex = $row;
            // Get all headers
            for ($col = 'A'; $col <= $highestCol; $col++) {
                $hVal = trim($sheet->getCell($col . $row)->getValue() ?? '');
                $headers[$col] = $hVal;
            }
            // Find next non-empty data row
            for ($dRow = $row + 1; $dRow <= $highestRow; $dRow++) {
                $valDA = trim($sheet->getCell('A' . $dRow)->getValue() ?? '');
                if (is_numeric($valDA)) {
                    for ($col = 'A'; $col <= $highestCol; $col++) {
                        $sampleRow[$col] = trim($sheet->getCell($col . $dRow)->getValue() ?? '');
                    }
                    break;
                }
            }
            break;
        }
    }
    
    echo "==================================================================\n";
    echo "Sheet: '$name'\n";
    echo "Header Row found at Index: $headerRowIndex\n";
    echo "Headers:\n";
    foreach ($headers as $col => $val) {
        if ($val !== '') {
            echo "  $col => '$val'\n";
        }
    }
    echo "Sample Data Row:\n";
    foreach ($sampleRow as $col => $val) {
        if ($val !== '' || isset($headers[$col])) {
            $hName = $headers[$col] ?? 'Col '.$col;
            echo "  $col ($hName) => '$val'\n";
        }
    }
    echo "\n";
}
