<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RuanganTemplateExport implements WithMultipleSheets, WithCustomCsvSettings
{
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\r\n",
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => false,
        ];
    }

    public function sheets(): array
    {
        return [
            new RuanganTemplateDataSheet(),
            new RuanganTemplateGuideSheet(),
        ];
    }
}

class RuanganTemplateDataSheet implements FromArray, WithTitle, WithColumnWidths, ShouldAutoSize, WithCustomCsvSettings, WithEvents
{
    public function title(): string
    {
        return 'Template Import Ruangan';
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\r\n",
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => false,
        ];
    }

    public function columnWidths(): array
    {
        $rows = $this->array();
        $colMaxLengths = [];
        
        // Logika dinamis: panjang kolom mengikuti teks terpanjang dari data
        foreach ($rows as $row) {
            if (empty($row) || (isset($row[0]) && str_starts_with((string)$row[0], '#'))) {
                continue;
            }
            foreach ($row as $colIdx => $val) {
                $len = mb_strlen((string)$val);
                $colMaxLengths[$colIdx] = max($colMaxLengths[$colIdx] ?? 0, $len);
            }
        }

        $widths = [];
        foreach ($colMaxLengths as $idx => $maxLen) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $widths[$colLetter] = max(14, $maxLen + 4);
        }

        return $widths;
    }

    public function array(): array
    {
        return [
            // Row 1: Header
            [
                'Nama Ruangan *',
                'Kode Ruangan',
                'Deskripsi / Lokasi',
                'Penanggung Jawab',
            ],
            // Sample Row 1
            [
                'Ruang Kepala Desa',
                'R-01',
                'Gedung Utama Lantai 1, Sebelah Timur',
                'Kepala Desa',
            ],
            // Sample Row 2
            [
                'Ruang Pelayanan Administrasi',
                'R-02',
                'Gedung Depan Balai Desa',
                'Sekretaris Desa',
            ],
            // Sample Row 3
            [
                'Gudang Inventaris Utama',
                'G-01',
                'Sayap Barat Belakang Kantor Desa',
                'Kaur Umum',
            ],
            // Baris kosong spasi pemisah antara data contoh dan petunjuk
            ['', '', '', ''],
            // Petunjuk Pengisian Berkas
            [
                '# [PETUNJUK PENGISIAN]',
                'Baris 1 adalah Header (JANGAN DIUBAH). Hapus baris contoh atau ganti dengan data riil ruangan Anda.',
                '',
                'Pengisian data dimulai dari baris 2 ke bawah.',
            ],
            [
                '# [PANDUAN FORMAT]',
                'Berkas resmi disimpan dan diunggah dalam format CSV (.csv).',
                '',
                'Format CSV (.csv) resmi sistem.',
            ],
            [
                '# [CATATAN SISTEM]',
                'Baris kosong dan baris yang diawali tanda # akan otomatis diabaikan sistem saat proses import.',
                '',
                'Pastikan kolom dan baris telah sesuai dengan format data.',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Logika auto-size kolom dan row height mengikuti isi teks
                $highestCol = $sheet->getHighestDataColumn();
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getDefaultRowDimension()->setRowHeight(-1);

                // Style Header (Row 1)
                $sheet->getRowDimension(1)->setRowHeight(32);
                $headerRange = 'A1:D1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F3A2A'], // SIMA Forest Green
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1F3320'],
                        ],
                    ],
                ]);

                // Style Sample Rows (Rows 2 - 4)
                for ($row = 2; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(24);
                    $rowRange = "A{$row}:D{$row}";
                    $sheet->getStyle($rowRange)->applyFromArray([
                        'font' => [
                            'name' => 'Segoe UI',
                            'size' => 10,
                            'color' => ['rgb' => '1F3320'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CFDCC8'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);

                    // Zebra background for samples
                    if ($row % 2 === 1) {
                        $sheet->getStyle($rowRange)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }

                // Baris kosong spasi pemisah (Row 5)
                $sheet->getRowDimension(5)->setRowHeight(18);

                // Style Baris Petunjuk (Rows 6 - 8)
                for ($pRow = 6; $pRow <= 8; $pRow++) {
                    $sheet->getRowDimension($pRow)->setRowHeight(22);
                    $pRange = "A{$pRow}:D{$pRow}";
                    $sheet->getStyle($pRange)->applyFromArray([
                        'font' => [
                            'name' => 'Segoe UI',
                            'size' => 9.5,
                            'italic' => true,
                            'color' => ['rgb' => '5A6E59'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F4F6EC'],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Explicit text format for Kode Ruangan
                $sheet->getStyle('B2:B4')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

class RuanganTemplateGuideSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function title(): string
    {
        return 'Panduan Import';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 18,
            'C' => 30,
            'D' => 60,
        ];
    }

    public function array(): array
    {
        $users = User::orderBy('nama')->take(15)->pluck('nama')->implode(', ');
        if (empty($users)) {
            $users = 'Belum ada data user terdaftar';
        }

        return [
            ['PETUNJUK PENGISIAN IMPORT DATA RUANGAN & LOKASI ASET DESA', '', '', ''],
            ['', '', '', ''],
            ['Nama Kolom', 'Wajib / Opsional', 'Format / Pilihan Nilai', 'Penjelasan & Contoh'],
            ['Nama Ruangan *', 'Wajib', 'Teks nama ruangan', 'Nama ruangan atau lokasi penyimpanan aset. Contoh: Ruang Rapat BPD, Balai Dusun, Kantor Perbekel'],
            ['Kode Ruangan', 'Opsional', 'Teks kode unik', 'Kode unik identifikasi ruangan (contoh: R-01, LAB-01). Otomatis dilewati jika kode duplikat'],
            ['Deskripsi / Lokasi', 'Opsional', 'Teks detail posisi', 'Keterangan detail posisi gedung atau lantai ruangan. Contoh: Gedung Utama Lantai 1, Sebelah Timur'],
            ['Penanggung Jawab', 'Opsional', 'Nama Aparatur / User', 'Nama staf/aparatur yang bertanggung jawab atas ruangan tersebut (sesuai daftar User/Pejabat)'],
            ['', '', '', ''],
            ['KETENTUAN RESOLUSI DATA & DUPLIKASI', '', '', ''],
            ['1. Duplikasi Ruangan', 'Sistem Otomatis', 'Skip (Dilewati)', 'Ruangan dengan Nama atau Kode yang sudah ada di database akan otomatis DILEWATI dan tidak menimpa data yang sudah ada.'],
            ['2. Baris Contoh', 'Perhatian', 'Hapus sebelum upload', 'Baris contoh pada Sheet 1 (baris 2-4) dapat dihapus atau diganti dengan data ruangan riil kantor desa Anda.'],
            ['', '', '', ''],
            ['DAFTAR REFERENSI SISTEM SAAT INI', '', '', ''],
            ['Kategori Referensi', 'Nilai / Pilihan yang Berlaku di Sistem', '', ''],
            ['Daftar Aparatur / Penanggung Jawab Terdaftar', $users, '', ''],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title Banner (Row 1)
                $sheet->mergeCells('A1:D1');
                $sheet->getRowDimension(1)->setRowHeight(36);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F3A2A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Table 1 Header (Row 3)
                $sheet->getRowDimension(3)->setRowHeight(28);
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '476846'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1F3320'],
                        ],
                    ],
                ]);

                // Table 1 Column Specs (Rows 4-7)
                for ($r = 4; $r <= 7; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(28);
                    $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                        'font' => ['name' => 'Segoe UI', 'size' => 9.5],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CFDCC8'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);
                    if ($r % 2 === 1) {
                        $sheet->getStyle("A{$r}:D{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }
                $sheet->getStyle('B4:B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:C7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Section Headers (Rows 9 & 13)
                $sectionRows = [9, 13];
                foreach ($sectionRows as $sr) {
                    $sheet->mergeCells("A{$sr}:D{$sr}");
                    $sheet->getRowDimension($sr)->setRowHeight(28);
                    $sheet->getStyle("A{$sr}")->applyFromArray([
                        'font' => [
                            'name' => 'Segoe UI',
                            'bold' => true,
                            'size' => 11,
                            'color' => ['rgb' => '1F3A2A'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D5E4D7'],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CFDCC8'],
                            ],
                        ],
                    ]);
                }

                // Table 2: Ketentuan Resolusi Data (Rows 10-11)
                for ($r = 10; $r <= 11; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(36);
                    $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                        'font' => ['name' => 'Segoe UI', 'size' => 9.5],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CFDCC8'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);
                    if ($r % 2 === 1) {
                        $sheet->getStyle("A{$r}:D{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }
                $sheet->getStyle('B10:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C10:C11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Table 3: Header Referensi (Row 14)
                $sheet->mergeCells('B14:D14');
                $sheet->getRowDimension(14)->setRowHeight(26);
                $sheet->getStyle('A14:D14')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '476846'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1F3320'],
                        ],
                    ],
                ]);

                // Table 3: Rows Referensi (Row 15)
                $sheet->mergeCells('B15:D15');
                $sheet->getRowDimension(15)->setRowHeight(64);
                $sheet->getStyle('A15:D15')->applyFromArray([
                    'font' => ['name' => 'Segoe UI', 'size' => 9.5],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CFDCC8'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getStyle('A15')->getFont()->setBold(true);
            },
        ];
    }
}
