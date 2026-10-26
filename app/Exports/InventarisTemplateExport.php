<?php

namespace App\Exports;

use App\Models\JenisInventaris;
use App\Models\Ruangan;
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

class InventarisTemplateExport implements WithMultipleSheets, WithCustomCsvSettings
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
            new InventarisTemplateDataSheet(),
            new InventarisTemplateGuideSheet(),
        ];
    }
}

class InventarisTemplateDataSheet implements FromArray, WithTitle, WithColumnWidths, ShouldAutoSize, WithCustomCsvSettings, WithEvents
{
    public function title(): string
    {
        return 'Template Import Aset';
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
        
        // Logika dinamis: panjang kolom mengikuti panjang teks terpanjang dari tiap kolom
        foreach ($rows as $row) {
            // Baris kosong atau petunjuk diabaikan dari penentuan lebar kolom data
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
            // Panjang kolom mengikuti panjang teks terpanjang + padding lega
            $widths[$colLetter] = max(14, $maxLen + 4);
        }

        return $widths;
    }

    public function array(): array
    {
        return [
            // Row 1: Header
            [
                'Kode Barang *',
                'Nama Barang *',
                'NUP *',
                'Jenis Inventaris *',
                'Tahun Perolehan *',
                'Bulan Perolehan',
                'Nilai Perolehan',
                'Kondisi *',
                'Asal Usul *',
                'Satuan',
                'Ruangan',
                'Pengguna',
                'Bukti Kepemilikan',
                'Keterangan',
            ],
            // Sample Row 1 (Peralatan dan Mesin)
            [
                '02.03.01.01.001',
                'Laptop ASUS ExpertBook B1400',
                '1',
                'Inventaris Peralatan dan Mesin',
                '2023',
                '3',
                '12500000',
                'Baik',
                'APBDes',
                'Unit',
                'Ruang Sekretaris Desa',
                'Sekretaris Desa',
                'Faktur Pembelian No. 042/INV/2023',
                'Laptop kerja administrasi desa',
            ],
            // Sample Row 2 (Kendaraan)
            [
                '02.02.01.02.001',
                'Sepeda Motor Honda Vario 125cc',
                '1',
                'Inventaris Kendaraan',
                '2022',
                '6',
                '24000000',
                'Baik',
                'APBDes',
                'Unit',
                'Gudang Inventaris Utama',
                'Kasi Pemerintahan',
                'BPKB No. 12938472',
                'Kendaraan operasional perangkat desa',
            ],
            // Sample Row 3 (Tanah)
            [
                '01.01.01.01.001',
                'Tanah Kas Desa Blok Selatan',
                '1',
                'Inventaris Tanah',
                '2019',
                '1',
                '250000000',
                'Baik',
                'Kekayaan Asli',
                'Bidang',
                '',
                'Pemerintah Desa',
                'Sertifikat Hak Pakai No. 14/Desa/2019',
                'Tanah pekarangan aset desa',
            ],
            // Sample Row 4 (Gedung)
            [
                '03.01.01.01.001',
                'Gedung Kantor Pelayanan Desa',
                '1',
                'Inventaris Gedung dan Bangunan',
                '2018',
                '1',
                '450000000',
                'Baik',
                'Kekayaan Asli',
                'Unit',
                '',
                'Pemerintah Desa',
                'IMB No. 05/IMB/2018',
                'Gedung balai pelayanan masyarakat',
            ],
            // Baris kosong spasi pemisah antara data contoh dan petunjuk
            ['', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            // Petunjuk Pengisian Berkas
            [
                '# [PETUNJUK PENGISIAN]',
                'Baris 1 adalah Header (JANGAN DIUBAH). Hapus baris contoh atau ganti dengan data riil aset desa Anda.',
                '', '', '', '', '', '', '', '', '', '', '',
                'Pengisian data dimulai dari baris 2 ke bawah.',
            ],
            [
                '# [PANDUAN FORMAT]',
                'Berkas resmi disimpan dan diunggah dalam format CSV (.csv).',
                '', '', '', '', '', '', '', '', '', '', '',
                'Format CSV (.csv) resmi sistem.',
            ],
            [
                '# [CATATAN SISTEM]',
                'Baris kosong dan baris yang diawali tanda # akan otomatis diabaikan sistem saat proses import.',
                '', '', '', '', '', '', '', '', '', '', '',
                'Pastikan kolom dan baris telah sesuai dengan format data.',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Logika dinamis: auto-size kolom dan tinggi baris mengikuti isi teks
                $highestCol = $sheet->getHighestDataColumn();
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getDefaultRowDimension()->setRowHeight(-1);

                // Style Header (Row 1)
                $sheet->getRowDimension(1)->setRowHeight(32);
                $headerRange = 'A1:N1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
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

                // Style Sample Rows (Rows 2 - 5)
                for ($row = 2; $row <= 5; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(24);
                    $rowRange = "A{$row}:N{$row}";
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

                // Baris kosong spasi pemisah (Row 6)
                $sheet->getRowDimension(6)->setRowHeight(18);

                // Style Baris Petunjuk (Rows 7 - 9)
                for ($pRow = 7; $pRow <= 9; $pRow++) {
                    $sheet->getRowDimension($pRow)->setRowHeight(22);
                    $pRange = "A{$pRow}:N{$pRow}";
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

                // Explicit text format for Kode Barang (A) and NUP (C) to avoid scientific notation
                $sheet->getStyle('A2:A5')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('C2:C5')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('E2:E5')->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('F2:F5')->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('G2:G5')->getNumberFormat()->setFormatCode('#,##0');

                // Alignments
                $sheet->getStyle('A2:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C2:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E2:E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F2:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H2:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I2:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J2:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G2:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}

class InventarisTemplateGuideSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
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
            'C' => 36,
            'D' => 60,
        ];
    }

    public function array(): array
    {
        $jenisList = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('id')
            ->pluck('nama')
            ->toArray();

        $ruanganList = Ruangan::orderBy('id')
            ->get()
            ->map(fn($r) => "{$r->nama} ({$r->kode})")
            ->toArray();

        $rows = [
            ['PETUNJUK PENGISIAN IMPORT DATA INVENTARIS ASET DESA', '', '', ''],
            ['', '', '', ''],
            ['Nama Kolom', 'Wajib / Opsional', 'Format / Pilihan Nilai', 'Penjelasan & Contoh'],
            ['Kode Barang *', 'Wajib', 'Teks klasifikasi barang', 'Kode klasifikasi barang sesuai Permendagri. Contoh: 02.03.01.01.001'],
            ['Nama Barang *', 'Wajib', 'Teks nama lengkap barang', 'Nama jelas aset/barang. Contoh: Laptop ASUS ExpertBook B1400'],
            ['NUP *', 'Wajib', 'Angka bulat (>= 1)', 'Nomor Urut Pendaftaran per kode barang. Contoh: 1, 2, 3'],
            ['Jenis Inventaris *', 'Wajib', 'Nama Jenis yang terdaftar', 'Pilih dari daftar Jenis Inventaris yang ada di sistem (lihat tabel di bawah)'],
            ['Tahun Perolehan *', 'Wajib', '4 Digit Angka (YYYY)', 'Tahun saat barang diperoleh/dibeli. Contoh: 2023'],
            ['Bulan Perolehan', 'Opsional', 'Angka 1 s/d 12', 'Bulan perolehan untuk laporan semester (1=Jan, 12=Des). Default: 1 jika kosong'],
            ['Nilai Perolehan', 'Opsional', 'Angka rupiah murni', 'Harga/nilai perolehan barang. Contoh: 12500000 (tanpa titik desimal)'],
            ['Kondisi *', 'Wajib', 'Baik / Rusak Ringan / Rusak Berat', 'Pilih salah satu kondisi aset saat ini'],
            ['Asal Usul *', 'Wajib', 'APBDes / Kekayaan Asli / Perolehan Lain', 'Pilih salah satu sumber asal-usul perolehan barang'],
            ['Satuan', 'Opsional', 'Teks satuan', 'Contoh: Unit, Buah, Set, Bidang, Paket'],
            ['Ruangan', 'Opsional', 'Nama atau Kode Ruangan', 'Isi nama atau kode ruangan tempat aset berada. Boleh dikosongkan jika aset luar ruangan'],
            ['Pengguna', 'Opsional', 'Teks nama pemakai', 'Nama pemakai atau pemegang tanggung jawab barang'],
            ['Bukti Kepemilikan', 'Opsional', 'Teks nomor dokumen', 'Contoh: Faktur No. 042/INV/2023, Sertifikat HP No. 14, BPKB'],
            ['Keterangan', 'Opsional', 'Teks catatan tambahan', 'Catatan penjelasan pengadaan atau kondisi aset'],
            ['', '', '', ''],
            ['KETENTUAN RESOLUSI DATA & DUPLIKASI', '', '', ''],
            ['1. Duplikasi Kode + NUP', 'Sistem Otomatis', 'Skip (Dilewati)', 'Jika kombinasi Kode Barang dan NUP sudah ada di database atau ganda dalam file, baris tersebut akan otomatis DILEWATI dan dilaporkan setelah impor selesai.'],
            ['2. Baris Contoh', 'Perhatian', 'Hapus sebelum upload', 'Baris contoh pada Sheet 1 (baris 2-5) dapat dihapus atau diganti dengan data riil desa Anda.'],
            ['', '', '', ''],
            ['DAFTAR REFERENSI SISTEM SAAT INI', '', '', ''],
            ['Kategori Referensi', 'Nilai / Pilihan yang Berlaku di Sistem', '', ''],
            ['Jenis Inventaris Aktif', implode(', ', $jenisList), '', ''],
            ['Ruangan Aktif', implode(', ', $ruanganList), '', ''],
            ['Pilihan Kondisi', 'Baik, Rusak Ringan, Rusak Berat', '', ''],
            ['Pilihan Asal Usul', 'APBDes, Kekayaan Asli, Perolehan Lain', '', ''],
        ];

        return $rows;
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

                // Table 1 Column Specs (Rows 4-17)
                for ($r = 4; $r <= 17; $r++) {
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
                $sheet->getStyle('B4:B17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Section Headers (Rows 19 & 23)
                $sectionRows = [19, 23];
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

                // Table 2: Ketentuan Resolusi Data (Rows 20-21)
                for ($r = 20; $r <= 21; $r++) {
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
                $sheet->getStyle('B20:B21')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C20:C21')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Table 3: Header Referensi (Row 24)
                $sheet->mergeCells('B24:D24');
                $sheet->getRowDimension(24)->setRowHeight(26);
                $sheet->getStyle('A24:D24')->applyFromArray([
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

                // Table 3: Rows Referensi (Rows 25-28)
                $refHeights = [
                    25 => 50, // Jenis Inventaris (wraps across 2-3 lines)
                    26 => 72, // Ruangan Aktif (wraps across 3-4 lines)
                    27 => 28, // Pilihan Kondisi
                    28 => 28, // Pilihan Asal Usul
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
