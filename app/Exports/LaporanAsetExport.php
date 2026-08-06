<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanAsetExport implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    protected array $rows;
    protected int   $tahun;
    protected string $periode;
    protected string $labelAkhir;

    public function __construct(array $rows, int $tahun, string $periode, string $labelAkhir)
    {
        $this->rows       = $rows;
        $this->tahun      = $tahun;
        $this->periode    = $periode;
        $this->labelAkhir = $labelAkhir;
    }

    public function title(): string
    {
        if ($this->periode === 'semester_1') {
            return 'LAPORAN SEMESTER I ' . $this->tahun;
        } elseif ($this->periode === 'semester_2') {
            return 'LAPORAN SEMESTER II ' . $this->tahun;
        }
        return 'LAPORAN ASET TAHUN ' . $this->tahun;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->rows as $r) {
            $data[] = [
                $r['kode'],
                $r['nama'],
                $r['satuan'],
                $r['saldo_kwant'] ?: 0,
                $r['saldo_nilai'] > 0 ? $r['saldo_nilai'] : 0,
                $r['tambah_kwant'] ?: 0,
                $r['tambah_nilai'] > 0 ? $r['tambah_nilai'] : 0,
                $r['kurang_kwant'] ?: 0,
                $r['kurang_nilai'] > 0 ? $r['kurang_nilai'] : 0,
                $r['akhir_kwant'] ?: 0,
                $r['akhir_nilai'] > 0 ? $r['akhir_nilai'] : 0,
            ];
        }
        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 28, 'C' => 10,
            'D' => 12, 'E' => 18,
            'F' => 12, 'G' => 18,
            'H' => 12, 'I' => 18,
            'J' => 12, 'K' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastCol  = 'K';
                $dataRows = count($this->rows);

                // Insert 8 baris header di atas
                $sheet->insertNewRowBefore(1, 8);

                $desa = config('desa');
                if ($this->periode === 'semester_1') {
                    $judulPeriode = 'SEMESTER I';
                } elseif ($this->periode === 'semester_2') {
                    $judulPeriode = 'SEMESTER II';
                } else {
                    $judulPeriode = 'PERTAHUN';
                }

                // Baris 1-3: Judul
                $sheet->setCellValue('A1', 'LAPORAN ASET DESA ' . strtoupper($desa['nama_lokasi'] ?? '') . ' ' . $judulPeriode);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $sheet->setCellValue('A2', 'RINCIAN PENGELOMPOKAN BARANG');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $sheet->setCellValue('A3', 'TAHUN ANGGARAN ' . $this->tahun);
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                foreach ([1,2,3] as $r) $sheet->getRowDimension($r)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(15); // Spasi antara judul dan tabel

                // Baris 5: Header level 1
                $sheet->setCellValue('A5', 'KODE');
                $sheet->mergeCells('A5:A7');
                $sheet->setCellValue('B5', 'NAMA BARANG');
                $sheet->mergeCells('B5:B7');
                $sheet->setCellValue('C5', 'SATUAN');
                $sheet->mergeCells('C5:C7');
                $tanggalSaldoAwal = ($this->periode === 'semester_2') ? '1 JULI ' : '1 JANUARI ';
                $sheet->setCellValue('D5', 'SALDO PER ' . $tanggalSaldoAwal . $this->tahun);
                $sheet->mergeCells('D5:E6'); // Merge dengan row 6 juga
                $sheet->setCellValue('F5', 'MUTASI');
                $sheet->mergeCells('F5:I5');
                $sheet->setCellValue('J5', 'SALDO PER ' . strtoupper($this->labelAkhir));
                $sheet->mergeCells('J5:K6'); // Merge dengan row 6 juga

                // Baris 6: Header level 2
                $sheet->setCellValue('F6', 'BERTAMBAH');
                $sheet->mergeCells('F6:G6');
                $sheet->setCellValue('H6', 'BERKURANG');
                $sheet->mergeCells('H6:I6');

                // Baris 7: Sub-header
                foreach (['D7' => 'KWANTITAS', 'E7' => 'NILAI (RP)', 'F7' => 'KWANTITAS', 'G7' => 'NILAI (RP)', 'H7' => 'KWANTITAS', 'I7' => 'NILAI (RP)', 'J7' => 'KWANTITAS', 'K7' => 'NILAI (RP)'] as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                // Baris 8: Nomor kolom
                foreach (['A8' => '1', 'B8' => '2', 'C8' => '3', 'D8' => '4', 'E8' => '5', 'F8' => '6', 'G8' => '7', 'H8' => '8', 'I8' => '9', 'J8' => '10', 'K8' => '11'] as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                // Style header rows 5-8
                $sheet->getStyle("A5:{$lastCol}8")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                foreach ([5,6,7,8] as $r) $sheet->getRowDimension($r)->setRowHeight(28);

                // Style data rows
                $dataStart = 9;
                $dataEnd   = $dataStart + $dataRows - 1;
                if ($dataRows > 0) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$dataEnd}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['size' => 10],
                    ]);
                    // Rata TENGAH kolom KWANTITAS
                    foreach (['D','F','H','J'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    // Rata KANAN kolom NILAI
                    foreach (['E','G','I','K'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    // Format angka untuk kolom kwantitas (integer)
                    foreach (['D','F','H','J'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")->getNumberFormat()
                            ->setFormatCode('#,##0');
                    }
                    // Format angka untuk kolom nilai (desimal 2 digit)
                    foreach (['E','G','I','K'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                    }
                    for ($r = $dataStart; $r <= $dataEnd; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                    }
                }

                // Baris JUMLAH - Hanya untuk kolom NILAI, KWANTITAS dikosongkan
                $jumlahRow = $dataEnd + 1;
                $totSaldoN = $totTambahN = $totKurangN = $totAkhirN = 0;
                foreach ($this->rows as $r) {
                    $totSaldoN  += $r['saldo_nilai'];
                    $totTambahN += $r['tambah_nilai'];
                    $totKurangN += $r['kurang_nilai'];
                    $totAkhirN  += $r['akhir_nilai'];
                }
                $sheet->mergeCells("A{$jumlahRow}:C{$jumlahRow}");
                $sheet->setCellValue("A{$jumlahRow}", 'JUMLAH');
                // Kolom KWANTITAS dikosongkan (D, F, H, J)
                $sheet->setCellValue("D{$jumlahRow}", '');
                $sheet->setCellValue("E{$jumlahRow}", $totSaldoN);
                $sheet->setCellValue("F{$jumlahRow}", '');
                $sheet->setCellValue("G{$jumlahRow}", $totTambahN);
                $sheet->setCellValue("H{$jumlahRow}", '');
                $sheet->setCellValue("I{$jumlahRow}", $totKurangN);
                $sheet->setCellValue("J{$jumlahRow}", '');
                $sheet->setCellValue("K{$jumlahRow}", $totAkhirN);
                $sheet->getStyle("A{$jumlahRow}:{$lastCol}{$jumlahRow}")->applyFromArray([
                    'font'    => ['bold' => true, 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                // Format number untuk kolom NILAI saja
                foreach (['E','G','I','K'] as $col) {
                    $sheet->getStyle("{$col}{$jumlahRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("{$col}{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
                $sheet->getRowDimension($jumlahRow)->setRowHeight(22);

                // === TANDA TANGAN ===
                // Get pejabat data
                $service = new \App\Services\LaporanAsetService();
                $reflection = new \ReflectionClass($service);
                $method = $reflection->getMethod('getPejabatForLaporanAset');
                $method->setAccessible(true);
                $pejabats = $method->invoke($service);
                
                $perbekel = $pejabats['perbekel'] ?? null;
                $kaurTU = $pejabats['kaur_tu'] ?? null;
                
                $perbekelNama = trim((string)($perbekel['nama'] ?? ''));
                $kaurTUNama   = trim((string)($kaurTU['nama'] ?? ''));
                
                $showPerbekel = ($perbekelNama !== '');
                $showKaurTU   = ($kaurTUNama !== '');
                
                // Skip TTD jika keduanya kosong
                if ($showPerbekel || $showKaurTU) {
                    $ttdRow = $jumlahRow + 2;
                    
                    // Tanggal dan label
                    $namaLokasi = config('desa.nama_lokasi', '');
                    $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                              5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                              9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    $tglStr = date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
                    $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi . ', ' : '') . $tglStr;
                    
                    // Row: Label jabatan kiri
                    if ($showPerbekel) {
                        $sheet->setCellValue("A{$ttdRow}", 'Mengetahui,');
                        $sheet->mergeCells("A{$ttdRow}:E{$ttdRow}");
                        $sheet->getStyle("A{$ttdRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                    
                    // Row: Tanggal + label jabatan kanan
                    if ($showKaurTU) {
                        $sheet->setCellValue("G{$ttdRow}", $tanggalDisplay);
                        $sheet->mergeCells("G{$ttdRow}:K{$ttdRow}");
                        $sheet->getStyle("G{$ttdRow}")->applyFromArray([
                            'font' => ['size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                    
                    // Row: Nama jabatan
                    $ttdRow++;
                    if ($showPerbekel) {
                        $sheet->setCellValue("A{$ttdRow}", $perbekel['jabatan'] ?? 'Perbekel ' . (config('desa.nama_lokasi') ?: 'Mengwi'));
                        $sheet->mergeCells("A{$ttdRow}:E{$ttdRow}");
                        $sheet->getStyle("A{$ttdRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                    
                    if ($showKaurTU) {
                        $sheet->setCellValue("G{$ttdRow}", 'Pengelola Aset ( Kaur Umum)');
                        $sheet->mergeCells("G{$ttdRow}:K{$ttdRow}");
                        $sheet->getStyle("G{$ttdRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                    
                    // Row: Space untuk TTD (3 baris)
                    for ($i = 1; $i <= 3; $i++) {
                        $ttdRow++;
                        if ($showPerbekel) {
                            $sheet->mergeCells("A{$ttdRow}:E{$ttdRow}");
                        }
                        if ($showKaurTU) {
                            $sheet->mergeCells("G{$ttdRow}:K{$ttdRow}");
                        }
                    }
                    
                    // Row: Nama pejabat (underline)
                    $ttdRow++;
                    if ($showPerbekel) {
                        $sheet->setCellValue("A{$ttdRow}", $perbekel['nama']);
                        $sheet->mergeCells("A{$ttdRow}:E{$ttdRow}");
                        $sheet->getStyle("A{$ttdRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'underline' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                    
                    if ($showKaurTU) {
                        $sheet->setCellValue("G{$ttdRow}", $kaurTU['nama']);
                        $sheet->mergeCells("G{$ttdRow}:K{$ttdRow}");
                        $sheet->getStyle("G{$ttdRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'underline' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                }

                // Print settings
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
            },
        ];
    }
}
