<?php

namespace App\Exports;

use App\Models\Dropdown;
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

class DropdownTemplateExport implements WithMultipleSheets, WithCustomCsvSettings
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
            new DropdownTemplateDataSheet(),
            new DropdownTemplateGuideSheet(),
        ];
    }
}

class DropdownTemplateDataSheet implements FromArray, WithTitle, WithColumnWidths, ShouldAutoSize, WithCustomCsvSettings, WithEvents
{
    public function title(): string
    {
        return 'Template Import Dropdown';
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
                'Nama Dropdown *',
                'Deskripsi',
                'Pilihan Item *',
            ],
            // Sample Rows for "Satuan Barang"
            [
                'Satuan Barang',
                'Pilihan satuan fisik aset inventaris desa',
                'Unit',
            ],
            [
                'Satuan Barang',
                'Pilihan satuan fisik aset inventaris desa',
                'Buah',
            ],
            [
                'Satuan Barang',
                'Pilihan satuan fisik aset inventaris desa',
                'Set',
            ],
            // Sample Rows for "Sumber Dana Tambahan"
            [
                'Sumber Dana Inventaris',
                'Sumber perolehan barang inventaris desa',
                'Bantuan Keuangan Khusus (BKK)',
            ],
            [
                'Sumber Dana Inventaris',
                'Sumber perolehan barang inventaris desa',
                'Hibah Pihak Ketiga',
            ],
            // Baris kosong spasi pemisah antara data contoh dan petunjuk
            ['', '', ''],
            // Petunjuk Pengisian Berkas
            [
                '# [PETUNJUK PENGISIAN]',
                'Baris 1 adalah Header (JANGAN DIUBAH). Hapus baris contoh atau ganti dengan data riil dropdown Anda.',
                '',
            ],
            [
                '# [PANDUAN FORMAT]',
                'Berkas resmi disimpan dan diunggah dalam format CSV (.csv).',
                '',
            ],
            [
                '# [CATATAN SISTEM]',
                'Baris kosong dan baris yang diawali tanda # akan otomatis diabaikan sistem saat proses import.',
                '',
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

                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F3A2A'],
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

                for ($r = 2; $r <= 6; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(24);
                    $sheet->getStyle("A{$r}:C{$r}")->applyFromArray([
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

                    if ($r % 2 === 1) {
                        $sheet->getStyle("A{$r}:C{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }

                // Baris kosong spasi pemisah (Row 7)
                $sheet->getRowDimension(7)->setRowHeight(18);

                // Style Baris Petunjuk (Rows 8 - 10)
                for ($pRow = 8; $pRow <= 10; $pRow++) {
                    $sheet->getRowDimension($pRow)->setRowHeight(22);
                    $pRange = "A{$pRow}:C{$pRow}";
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
            },
        ];
    }
}

class DropdownTemplateGuideSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
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
        $existing = Dropdown::orderBy('nama')->take(10)->pluck('nama')->implode(', ');

        return [
            ['PETUNJUK PENGISIAN IMPORT DATA DROPDOWN & PILIHAN OPSI SISTEM', '', '', ''],
            ['', '', '', ''],
            ['Nama Kolom', 'Wajib / Opsional', 'Format / Pilihan Nilai', 'Penjelasan & Contoh'],
            ['Nama Dropdown *', 'Wajib', 'Teks nama dropdown', 'Nama grup / kategori dropdown. Contoh: Satuan Barang, Sumber Dana Tambahan, Jenis Dokumen'],
            ['Deskripsi', 'Opsional', 'Teks keterangan', 'Uraian fungsi dropdown di sistem. Contoh: Pilihan satuan fisik aset inventaris desa'],
            ['Pilihan Item *', 'Wajib', 'Teks nilai opsi', 'Label pilihan opsi item dropdown. Contoh: Unit, Buah, Set, Paket, Bantuan Keuangan Khusus (BKK)'],
            ['', '', '', ''],
            ['KETENTUAN RESOLUSI DATA & DUPLIKASI', '', '', ''],
            ['1. Kategori Baru & Update', 'Sistem Otomatis', 'Buat atau Gabung', 'Jika Nama Dropdown belum ada, sistem akan membuatkan kategori baru. Jika sudah ada, opsi item baru akan ditambahkan ke grup tersebut.'],
            ['2. Duplikasi Pilihan Item', 'Sistem Otomatis', 'Skip (Dilewati)', 'Jika Pilihan Item sudah pernah ada di dalam kategori dropdown tersebut, baris akan otomatis DILEWATI (tidak terjadi penggandaan item).'],
            ['3. Baris Contoh', 'Perhatian', 'Hapus sebelum upload', 'Baris contoh pada Sheet 1 (baris 2-6) dapat disesuaikan atau diganti dengan data opsi dropdown desa Anda.'],
            ['', '', '', ''],
            ['DAFTAR REFERENSI SISTEM SAAT INI', '', '', ''],
            ['Kategori Referensi', 'Nilai / Pilihan yang Berlaku di Sistem', '', ''],
            ['Dropdown Eksisting di Sistem', $existing ?: 'Belum ada data dropdown tersimpan', '', ''],
            ['Contoh Format Pengisian', 'Satu baris mewakili satu Pilihan Item. Untuk kategori yang sama dengan multi opsi, ulangi Nama Dropdown di setiap baris.', '', ''],
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

                // Table 1 Column Specs (Rows 4-6)
                for ($r = 4; $r <= 6; $r++) {
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
                $sheet->getStyle('B4:B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Section Headers (Rows 8 & 13)
                $sectionRows = [8, 13];
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

                // Table 2: Ketentuan Resolusi Data (Rows 9-11)
                for ($r = 9; $r <= 11; $r++) {
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
                $sheet->getStyle('B9:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C9:C11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

                // Table 3: Rows Referensi (Rows 15-16)
                $refHeights = [
                    15 => 50,
                    16 => 36,
                ];

                foreach ($refHeights as $r => $h) {
                    $sheet->mergeCells("B{$r}:D{$r}");
                    $sheet->getRowDimension($r)->setRowHeight($h);
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
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
                    if ($r % 2 === 1) {
                        $sheet->getStyle("A{$r}:D{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }
            },
        ];
    }
}
