<?php

namespace App\Exports;

use App\Models\Role;
use App\Models\Bidang;
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

class UserTemplateExport implements WithMultipleSheets, WithCustomCsvSettings
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
            new UserTemplateDataSheet(),
            new UserTemplateGuideSheet(),
        ];
    }
}

class UserTemplateDataSheet implements FromArray, WithTitle, WithColumnWidths, ShouldAutoSize, WithCustomCsvSettings, WithEvents
{
    public function title(): string
    {
        return 'Template Import Pengguna';
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
                'Nama Lengkap *',
                'Username (Unik) *',
                'Peran / Role *',
                'Bidang (Khusus Kaur)',
                'Status Akun (Aktif / Nonaktif)',
                'Alamat Domisili',
                'Password Awal (Opsional)',
            ],
            // Sample Row 1
            [
                'I Wayan Sudarma',
                'wayan.sudarma',
                'Perbekel',
                '',
                'Aktif',
                'Banjar Dinas Kelod',
                'password123',
            ],
            // Sample Row 2
            [
                'Ni Made Laksmi',
                'made.laksmi',
                'Sekretaris Desa',
                '',
                'Aktif',
                'Banjar Dinas Kaja',
                'password123',
            ],
            // Sample Row 3
            [
                'I Gede Arya Pratama',
                'gede.arya',
                'Kaur',
                'Bidang Pemerintahan Desa',
                'Aktif',
                'Banjar Dinas Tengah',
                'password123',
            ],
            // Baris kosong spasi pemisah antara data contoh dan petunjuk
            ['', '', '', '', '', '', ''],
            // Petunjuk Pengisian Berkas
            [
                '# [PETUNJUK PENGISIAN]',
                'Baris 1 adalah Header (JANGAN DIUBAH). Hapus baris contoh atau ganti dengan data riil pengguna Anda.',
                '', '', '', '',
                'Pengisian data dimulai dari baris 2 ke bawah.',
            ],
            [
                '# [PANDUAN FORMAT]',
                'Berkas resmi disimpan dan diunggah dalam format CSV (.csv).',
                '', '', '', '',
                'Format CSV (.csv) resmi sistem.',
            ],
            [
                '# [CATATAN SISTEM]',
                'Baris kosong dan baris yang diawali tanda # akan otomatis diabaikan sistem saat proses import.',
                '', '', '', '',
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
                $headerRange = 'A1:G1';
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
                    $rowRange = "A{$row}:G{$row}";
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
                    $pRange = "A{$pRow}:G{$pRow}";
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

                // Explicit text format for Username and Password
                $sheet->getStyle('B2:B4')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('G2:G4')->getNumberFormat()->setFormatCode('@');

                // Alignments
                $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C2:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E2:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

class UserTemplateGuideSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
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
        $roles = Role::orderBy('nama')->pluck('nama')->map(function ($r) {
            return ucwords(str_replace('_', ' ', $r));
        })->implode(', ');

        $bidangs = Bidang::orderBy('nama')->pluck('nama')->implode(', ');

        return [
            ['PETUNJUK PENGISIAN IMPORT DATA PENGGUNA & AKUN STAF DESA', '', '', ''],
            ['', '', '', ''],
            ['Nama Kolom', 'Wajib / Opsional', 'Format / Pilihan Nilai', 'Penjelasan & Contoh'],
            ['Nama Lengkap *', 'Wajib', 'Teks nama lengkap', 'Nama lengkap aparatur/staf desa. Contoh: I Wayan Sudarma, Ni Made Laksmi'],
            ['Username (Unik) *', 'Wajib', 'Teks (min. 3 karakter)', 'Username unik untuk login sistem tanpa spasi. Contoh: wayan.sudarma, made.laksmi'],
            ['Peran / Role *', 'Wajib', 'Nama Peran terdaftar', 'Pilih dari daftar peran yang berlaku: Super Admin, Sekretaris Desa, Kaur, Kasi, Perbekel, Kelian Banjar, Staf'],
            ['Bidang (Khusus Kaur)', 'Opsional', 'Nama Bidang terdaftar', 'Wajib diisi jika peran adalah Kaur atau Kasi untuk menentukan bidang tugasnya'],
            ['Status Akun (Aktif / Nonaktif)', 'Opsional', 'Aktif / Nonaktif', 'Status hak akses login akun. Default: Aktif jika dikosongkan'],
            ['Alamat Domisili', 'Opsional', 'Teks alamat', 'Alamat tempat tinggal staf/aparatur desa. Contoh: Banjar Dinas Kelod'],
            ['Password Awal (Opsional)', 'Opsional', 'Teks (min. 8 karakter)', 'Kata sandi awal akun. Jika dikosongkan, default otomatis: "password123"'],
            ['', '', '', ''],
            ['KETENTUAN RESOLUSI DATA & DUPLIKASI', '', '', ''],
            ['1. Duplikasi Username', 'Sistem Otomatis', 'Skip (Dilewati)', 'Pengguna dengan Username yang sudah terdaftar di database akan otomatis DILEWATI dan tidak menimpa akun yang sudah ada.'],
            ['2. Baris Contoh', 'Perhatian', 'Hapus sebelum upload', 'Baris contoh pada Sheet 1 (baris 2-4) dapat dihapus atau diganti dengan data staf desa riil Anda.'],
            ['', '', '', ''],
            ['DAFTAR REFERENSI SISTEM SAAT INI', '', '', ''],
            ['Kategori Referensi', 'Nilai / Pilihan yang Berlaku di Sistem', '', ''],
            ['Daftar Role / Peran Aktif', $roles ?: 'Super Admin, Sekretaris Desa, Kaur, Kasi, Perbekel, Kelian Banjar, Staf', '', ''],
            ['Daftar Bidang Kerja Aktif', $bidangs ?: 'Bidang Pemerintahan Desa, Bidang Keuangan, Bidang Perencanaan, Bidang Umum dan Protokol', '', ''],
            ['Pilihan Status Akun', 'Aktif, Nonaktif', '', ''],
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

                // Table 1 Column Specs (Rows 4-10)
                for ($r = 4; $r <= 10; $r++) {
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
                $sheet->getStyle('B4:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:C10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Section Headers (Rows 12 & 16)
                $sectionRows = [12, 16];
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

                // Table 2: Ketentuan Resolusi Data (Rows 13-14)
                for ($r = 13; $r <= 14; $r++) {
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
                $sheet->getStyle('B13:B14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C13:C14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Table 3: Header Referensi (Row 17)
                $sheet->mergeCells('B17:D17');
                $sheet->getRowDimension(17)->setRowHeight(26);
                $sheet->getStyle('A17:D17')->applyFromArray([
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

                // Table 3: Rows Referensi (Rows 18-20)
                $refHeights = [
                    18 => 48, // Role list
                    19 => 64, // Bidang list
                    20 => 28, // Status Akun
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
