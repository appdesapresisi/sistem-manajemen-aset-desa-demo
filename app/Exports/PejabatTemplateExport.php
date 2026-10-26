<?php

namespace App\Exports;

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

class PejabatTemplateExport implements WithMultipleSheets, WithCustomCsvSettings
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
            new PejabatTemplateDataSheet(),
            new PejabatTemplateGuideSheet(),
        ];
    }
}

class PejabatTemplateDataSheet implements FromArray, WithTitle, WithColumnWidths, ShouldAutoSize, WithCustomCsvSettings, WithEvents
{
    public function title(): string
    {
        return 'Template Import Pejabat';
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
                'Nama Lengkap & Gelar *',
                'Jabatan *',
                'Tugas / Wewenang',
                'Alamat',
                'Petugas Pengurus Barang',
                'Status Aktif',
            ],
            // Sample Row 1 (Kepala Desa)
            [
                'I Wayan Sudarma, S.H.',
                'Perbekel / Kepala Desa',
                'Pengguna Barang',
                'Banjar Dinas Kelod RT 01',
                'Tidak',
                'Aktif',
            ],
            // Sample Row 2 (Sekretaris Desa)
            [
                'Ni Made Laksmi, S.Pd.',
                'Sekretaris Desa',
                'Pengelola Barang',
                'Banjar Dinas Kaja RT 02',
                'Tidak',
                'Aktif',
            ],
            // Sample Row 3 (Kaur Umum & Petugas Barang)
            [
                'I Gede Arya Pratama',
                'Kaur Tata Usaha & Umum',
                'Pengurus Barang',
                'Banjar Dinas Tengah RT 03',
                'Ya',
                'Aktif',
            ],
            // Baris kosong spasi pemisah antara data contoh dan petunjuk
            ['', '', '', '', '', ''],
            // Petunjuk Pengisian Berkas
            [
                '# [PETUNJUK PENGISIAN]',
                'Baris 1 adalah Header (JANGAN DIUBAH). Hapus baris contoh atau ganti dengan data riil pejabat Anda.',
                '', '', '',
                'Pengisian data dimulai dari baris 2 ke bawah.',
            ],
            [
                '# [PANDUAN FORMAT]',
                'Berkas resmi disimpan dan diunggah dalam format CSV (.csv).',
                '', '', '',
                'Format CSV (.csv) resmi sistem.',
            ],
            [
                '# [CATATAN SISTEM]',
                'Baris kosong dan baris yang diawali tanda # akan otomatis diabaikan sistem saat proses import.',
                '', '', '',
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
                $sheet->getStyle('A1:F1')->applyFromArray([
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

                // Sample Rows (Rows 2-4)
                for ($r = 2; $r <= 4; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(24);
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
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
                        $sheet->getStyle("A{$r}:F{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F8EC');
                    }
                }

                // Baris kosong spasi pemisah (Row 5)
                $sheet->getRowDimension(5)->setRowHeight(18);

                // Style Baris Petunjuk (Rows 6 - 8)
                for ($pRow = 6; $pRow <= 8; $pRow++) {
                    $sheet->getRowDimension($pRow)->setRowHeight(22);
                    $pRange = "A{$pRow}:F{$pRow}";
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

                $sheet->getStyle('E2:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F2:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

class PejabatTemplateGuideSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
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
        return [
            ['PETUNJUK PENGISIAN IMPORT DATA PEJABAT & APARATUR DESA', '', '', ''],
            ['', '', '', ''],
            ['Nama Kolom', 'Wajib / Opsional', 'Format / Pilihan Nilai', 'Penjelasan & Contoh'],
            ['Nama Lengkap & Gelar *', 'Wajib', 'Teks nama lengkap & gelar', 'Nama lengkap aparatur desa beserta gelar akademik jika ada. Contoh: I Wayan Sudarma, S.H.'],
            ['Jabatan *', 'Wajib', 'Teks nama jabatan resmi', 'Jabatan resmi pemerintahan desa. Contoh: Perbekel / Kepala Desa, Sekretaris Desa, Kaur Keuangan'],
            ['Tugas / Wewenang', 'Opsional', 'Teks tugas penatausahaan', 'Peran tugas penatausahaan aset. Contoh: Pengguna Barang, Pengelola Barang, Pengurus Barang'],
            ['Alamat', 'Opsional', 'Teks alamat domisili', 'Alamat tempat tinggal aparatur desa. Contoh: Banjar Dinas Kelod RT 01'],
            ['Petugas Pengurus Barang', 'Opsional', 'Ya / Tidak', 'Isi "Ya" atau "Tidak" apakah bertindak sebagai petugas pencatat barang. Default: Tidak'],
            ['Status Aktif', 'Opsional', 'Aktif / Nonaktif', 'Status keaktifan masa jabatan aparatur. Default: Aktif jika dikosongkan'],
            ['', '', '', ''],
            ['KETENTUAN RESOLUSI DATA & DUPLIKASI', '', '', ''],
            ['1. Duplikasi Nama & Jabatan', 'Sistem Otomatis', 'Skip (Dilewati)', 'Pejabat dengan kombinasi Nama Lengkap dan Jabatan yang sama persis di sistem akan otomatis DILEWATI dan tidak menimpa data yang ada.'],
            ['2. Tanda Tangan Digital', 'Informasi', 'Unggah manual', 'Tanda tangan digital (TTD) dapat diunggah kemudian melalui halaman Edit Pejabat pada menu Master Pejabat.'],
            ['3. Baris Contoh', 'Perhatian', 'Hapus sebelum upload', 'Baris contoh pada Sheet 1 (baris 2-4) dapat dihapus atau diganti dengan data riil aparatur desa Anda.'],
            ['', '', '', ''],
            ['DAFTAR REFERENSI SISTEM SAAT INI', '', '', ''],
            ['Kategori Referensi', 'Nilai / Pilihan yang Berlaku di Sistem', '', ''],
            ['Pilihan Tugas / Wewenang', 'Pengguna Barang, Pengelola Barang, Pengurus Barang', '', ''],
            ['Pilihan Petugas Pengurus Barang', 'Ya, Tidak (Default: Tidak)', '', ''],
            ['Pilihan Status Keaktifan', 'Aktif, Nonaktif (Default: Aktif)', '', ''],
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

                // Table 1 Column Specs (Rows 4-9)
                for ($r = 4; $r <= 9; $r++) {
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
                $sheet->getStyle('B4:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Section Headers (Rows 11 & 16)
                $sectionRows = [11, 16];
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

                // Table 2: Ketentuan Resolusi Data (Rows 12-14)
                for ($r = 12; $r <= 14; $r++) {
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
                $sheet->getStyle('B12:B14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C12:C14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                for ($r = 18; $r <= 20; $r++) {
                    $sheet->mergeCells("B{$r}:D{$r}");
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
