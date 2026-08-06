<?php
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFile = 'LAPORAN HASIL INVENTARISASI ASET DESA MENGWI TAHUN 2025.xlsx';
$reader = IOFactory::createReaderForFile($excelFile);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($excelFile);
$sheetNames = $spreadsheet->getSheetNames();

foreach ($sheetNames as $idx => $name) {
    if ($idx === 0) continue; // Skip summary sheet
    $sheet = $spreadsheet->getSheet($idx);
    
    // Find the header row index
    $headerRow = 0;
    for ($row = 1; $row <= 15; $row++) {
        $valA = trim($sheet->getCell('A' . $row)->getValue() ?? '');
        $valB = trim($sheet->getCell('B' . $row)->getValue() ?? '');
        if (strcasecmp($valA, 'No') === 0 || strcasecmp($valB, 'Nama Barang') === 0 || stripos($valB, 'Jenis') !== false) {
            $headerRow = $row;
            break;
        }
    }
    
    if ($headerRow) {
        echo "Sheet #$idx: '$name' (Header Row: $headerRow, Sub-header Row: " . ($headerRow+1) . ")\n";
        
        // Print all headers and sub-headers from columns A to AA
        for ($col = 'A'; $col <= 'Z'; $col++) {
            $hVal = trim($sheet->getCell($col . $headerRow)->getValue() ?? '');
            $subVal = trim($sheet->getCell($col . ($headerRow+1))->getValue() ?? '');
            
            if ($hVal !== '' || $subVal !== '') {
                echo "  $col: Header='{$hVal}', Sub='{$subVal}'\n";
            }
        }
        echo "\n";
    }
}
