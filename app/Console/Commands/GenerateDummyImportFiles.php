<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenerateDummyImportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:generate-dummy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate realistic dummy Excel (.xlsx) files for all 6 import modules in SIMA';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pembuatan berkas data dummy untuk seluruh fitur import...');

        $targetDirs = [
            base_path('dummy_import_files'),
            public_path('dummy_import_files'),
        ];

        foreach ($targetDirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $files = [
            '01_dummy_import_bidang.xlsx' => [
                'title' => 'Import Bidang',
                'headers' => ['Nama Bidang *', 'Deskripsi'],
                'widths' => ['A' => 45, 'B' => 60],
                'rows' => [
                    [
                        'Bidang Pariwisata dan Ekonomi Kreatif Desa',
                        'Pengelolaan potensi desa wisata, promosi kerajinan warga, dan event budaya desa',
                    ],
                    [
                        'Bidang Lingkungan Hidup dan Pengelolaan Sampah',
                        'Pengelolaan TPS3R desa, bank sampah terpadu, dan program penghijauan lingkungan',
                    ],
                    [
                        'Bidang Komunikasi dan Digitalisasi Desa',
                        'Pengembangan sistem keterbukaan informasi publik, layanan digital mandiri desa, dan literasi teknologi warga',
                    ],
                    [
                        'Bidang Pemuda dan Olahraga Desa',
                        'Pembinaan karang taruna, turnamen olahraga antar-banjar, dan pemeliharaan lapangan desa',
                    ],
                    [
                        'Bidang Ketahanan Pangan dan Pertanian',
                        'Pendampingan kelompok tani, lumbung bibit desa, dan perbaikan sarana irigasi subak',
                    ],
                ],
            ],

            '02_dummy_import_ruangan.xlsx' => [
                'title' => 'Import Ruangan',
                'headers' => ['Nama Ruangan *', 'Kode Ruangan', 'Deskripsi / Lokasi', 'Penanggung Jawab'],
                'widths' => ['A' => 35, 'B' => 18, 'C' => 50, 'D' => 30],
                'rows' => [
                    [
                        'Ruang Pengelola BUMDes',
                        'R-011',
                        'Gedung Komersial Desa Lantai 1, Sebelah Timur Pasar Desa',
                        'Direktur BUMDes',
                    ],
                    [
                        'Ruang Komputer dan Media Digital',
                        'R-012',
                        'Gedung Pelayanan Lantai 2, Depan Ruang Arsip',
                        'Staf Teknologi Informasi',
                    ],
                    [
                        'Gudang Alat Pertanian & Mesin Desa',
                        'R-013',
                        'Area Belakang Kantor Desa, Samping Garasi Mobil Operasional',
                        'Kaur Umum & Tata Usaha',
                    ],
                    [
                        'Ruang Pusat Kebugaran Warga Desa',
                        'R-014',
                        'Kompleks Lapangan Serbaguna Desa',
                        'Ketua Karang Taruna',
                    ],
                    [
                        'Ruang Koperasi Simpan Pinjam Desa',
                        'R-015',
                        'Gedung Sayap Barat Lantai 1',
                        'Pengurus Koperasi',
                    ],
                ],
            ],

            '03_dummy_import_pengguna.xlsx' => [
                'title' => 'Import Pengguna',
                'headers' => [
                    'Nama Lengkap *',
                    'Username (Unik) *',
                    'Peran / Role *',
                    'Bidang (Khusus Kaur)',
                    'Status Akun (Aktif / Nonaktif)',
                    'Alamat Domisili',
                    'Password Awal (Opsional)',
                ],
                'widths' => ['A' => 32, 'B' => 24, 'C' => 20, 'D' => 35, 'E' => 24, 'F' => 35, 'G' => 25],
                'rows' => [
                    [
                        'I Gusti Ngurah Bagus Pratama',
                        'ngurah.pratama',
                        'kaur',
                        'Bidang Keuangan',
                        'Aktif',
                        'Banjar Dinas Kangin RT 02',
                        'desa12345',
                    ],
                    [
                        'Ni Putu Eka Sintadewi, S.Kom.',
                        'putu.sintadewi',
                        'staf',
                        'Bidang Pemerintahan Desa',
                        'Aktif',
                        'Banjar Dinas Tengah RT 01',
                        'desa12345',
                    ],
                    [
                        'I Kadek Rian Saputra, S.E.',
                        'kadek.rian',
                        'kasi',
                        'Bidang Kesejahteraan Rakyat',
                        'Aktif',
                        'Banjar Dinas Pande RT 03',
                        'desa12345',
                    ],
                    [
                        'I Made Agus Dharmawan',
                        'agus.dharmawan',
                        'kelian_banjar',
                        'Bidang Pembinaan Kemasyarakatan',
                        'Aktif',
                        'Banjar Dinas Kauh RT 04',
                        'desa12345',
                    ],
                    [
                        'Ni Komang Wulandari, A.Md.',
                        'komang.wulan',
                        'staf',
                        'Bidang Pembangunan Desa',
                        'Aktif',
                        'Banjar Dinas Peken RT 02',
                        'desa12345',
                    ],
                ],
            ],

            '04_dummy_import_pejabat.xlsx' => [
                'title' => 'Import Pejabat',
                'headers' => [
                    'Nama Lengkap & Gelar *',
                    'Jabatan *',
                    'Tugas / Wewenang',
                    'Alamat',
                    'Petugas Pengurus Barang',
                    'Status Aktif',
                ],
                'widths' => ['A' => 32, 'B' => 35, 'C' => 45, 'D' => 30, 'E' => 24, 'F' => 18],
                'rows' => [
                    [
                        'I Nyoman Bagus Santosa, S.Pd.',
                        'Kepala Dusun / Kelian Banjar Kangin',
                        'Pembina ketentraman dan pendataan aset wilayah Banjar Kangin',
                        'Banjar Dinas Kangin RT 01',
                        'Tidak',
                        'Aktif',
                    ],
                    [
                        'Ni Made Ratih Kumalasari, S.E.',
                        'Kaur Perencanaan & Keuangan Desa',
                        'Penyusun rencana kerja dan verifikator administrasi aset desa',
                        'Banjar Dinas Tengah RT 03',
                        'Ya',
                        'Aktif',
                    ],
                    [
                        'I Ketut Gede Wira Adnyana, S.T.',
                        'Kasi Kesejahteraan dan Pembangunan',
                        'Pelaksana kegiatan fisik dan penanggung jawab sarana prasarana',
                        'Banjar Dinas Kauh RT 02',
                        'Tidak',
                        'Aktif',
                    ],
                    [
                        'Ni Wayan Sukerti, A.Md.',
                        'Staf Administrasi & Pengurus Barang Pembantu',
                        'Pencatatan mutasi barang, inventarisasi, dan pelabelan barcode',
                        'Banjar Dinas Pande RT 04',
                        'Ya',
                        'Aktif',
                    ],
                    [
                        'I Gede Ananda Putra, S.H.',
                        'Anggota Badan Permusyawaratan Desa (BPD)',
                        'Pengawas penyelenggaraan pemerintahan dan perlindungan aset desa',
                        'Banjar Dinas Kelod RT 02',
                        'Tidak',
                        'Aktif',
                    ],
                ],
            ],

            '05_dummy_import_dropdown.xlsx' => [
                'title' => 'Import Dropdown',
                'headers' => ['Nama Dropdown *', 'Deskripsi', 'Pilihan Item *'],
                'widths' => ['A' => 30, 'B' => 45, 'C' => 35],
                'rows' => [
                    [
                        'Kategori Aset Produktif',
                        'Klasifikasi pemanfaatan aset desa yang menghasilkan PADes',
                        'Sewa Lapak Pasar Desa',
                    ],
                    [
                        'Kategori Aset Produktif',
                        'Klasifikasi pemanfaatan aset desa yang menghasilkan PADes',
                        'Wisata Kuliner Desa',
                    ],
                    [
                        'Kategori Aset Produktif',
                        'Klasifikasi pemanfaatan aset desa yang menghasilkan PADes',
                        'Penyewaan Gedung Pertemuan',
                    ],
                    [
                        'Kategori Aset Produktif',
                        'Klasifikasi pemanfaatan aset desa yang menghasilkan PADes',
                        'Penyewaan Tenda dan Kursi',
                    ],
                    [
                        'Metode Pengadaan Aset',
                        'Cara desa memperoleh barang inventaris',
                        'Swakelola Desa',
                    ],
                    [
                        'Metode Pengadaan Aset',
                        'Cara desa memperoleh barang inventaris',
                        'Pelelangan Sederhana',
                    ],
                    [
                        'Metode Pengadaan Aset',
                        'Cara desa memperoleh barang inventaris',
                        'Bantuan Pemerintah Pusat',
                    ],
                    [
                        'Satuan Barang',
                        'Pilihan satuan fisik aset inventaris desa',
                        'Paket',
                    ],
                    [
                        'Satuan Barang',
                        'Pilihan satuan fisik aset inventaris desa',
                        'Rim',
                    ],
                    [
                        'Satuan Barang',
                        'Pilihan satuan fisik aset inventaris desa',
                        'Roll',
                    ],
                ],
            ],

            '06_dummy_import_inventaris.xlsx' => [
                'title' => 'Import Aset Desa',
                'headers' => [
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
                'widths' => [
                    'A' => 22,
                    'B' => 38,
                    'C' => 10,
                    'D' => 32,
                    'E' => 18,
                    'F' => 16,
                    'G' => 20,
                    'H' => 16,
                    'I' => 18,
                    'J' => 14,
                    'K' => 28,
                    'L' => 26,
                    'M' => 35,
                    'N' => 45,
                ],
                'rows' => [
                    [
                        '02.03.01.01.099',
                        'Drone Pemetaan Wilayah Desa DJI Mini 4 Pro',
                        '1',
                        'Inventaris Peralatan dan Mesin',
                        '2024',
                        '5',
                        '18500000',
                        'Baik',
                        'APBDes',
                        'Unit',
                        'Ruang Perbekel',
                        'Perbekel',
                        'Kwitansi No. 102/DRN/2024',
                        'Alat dokumentasi dan pemetaan batas wilayah desa',
                    ],
                    [
                        '02.02.01.02.088',
                        'Sepeda Motor Dinas Honda PCX 160cc',
                        '1',
                        'Inventaris Kendaraan',
                        '2023',
                        '8',
                        '33500000',
                        'Baik',
                        'APBDes',
                        'Unit',
                        'Ruang Sekretaris Desa',
                        'Sekretaris Desa',
                        'BPKB No. N-8921820-2023',
                        'Kendaraan operasional dinas sekretariat desa',
                    ],
                    [
                        '02.06.01.02.077',
                        'Sound System Portable Wireless Baretone 15 Inch',
                        '1',
                        'Inventaris Peralatan dan Mesin',
                        '2023',
                        '11',
                        '4800000',
                        'Baik',
                        'APBDes',
                        'Set',
                        'Aula Pertemuan Desa',
                        'Kaur Umum & Tata Usaha',
                        'Nota Pembelian No. 77/ELK/2023',
                        'Pengeras suara kegiatan sangkep dan pertemuan warga',
                    ],
                    [
                        '01.01.01.04.066',
                        'Tanah Lapangan Olahraga Serbaguna Desa',
                        '1',
                        'Inventaris Tanah',
                        '2021',
                        '1',
                        '450000000',
                        'Baik',
                        'Kekayaan Asli',
                        'Bidang',
                        'Gudang Inventaris Utama',
                        'Pemerintah Desa',
                        'Sertipikat Hak Pakai No. 00214/Desa',
                        'Tanah fasilitas olahraga dan ruang terbuka hijau desa',
                    ],
                    [
                        '03.07.01.01.055',
                        'Gedung Balai Budaya dan Kesenian Banjar',
                        '1',
                        'Inventaris Gedung dan Bangunan',
                        '2022',
                        '10',
                        '275000000',
                        'Baik',
                        'APBDes',
                        'Unit',
                        'Aula Pertemuan Desa',
                        'Masyarakat Desa',
                        'Berita Acara Serah Terima No. 44/BAST/2022',
                        'Pusat latihan tabuh dan sanggar seni desa',
                    ],
                    [
                        '04.01.01.01.044',
                        'Jaringan Irigasi Subak Abian Sektor Timur',
                        '1',
                        'Jalan, Irigasi & Jaringan',
                        '2023',
                        '6',
                        '85000000',
                        'Baik',
                        'APBDes',
                        'Meter',
                        'Pos Keamanan Linmas',
                        'Kelompok Tani Desa',
                        'Laporan Selesai Fisik No. 12/IRG/2023',
                        'Saluran irigasi pengairan lahan sawah desa sepanjang 450m',
                    ],
                ],
            ],
        ];

        foreach ($files as $filename => $config) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($config['title'], 0, 31));

            // Set Column Widths
            foreach ($config['widths'] as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Write Header
            $colCount = count($config['headers']);
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

            foreach ($config['headers'] as $idx => $headerText) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1) . '1';
                $sheet->setCellValue($cellCoord, $headerText);
            }

            // Style Header Row (SIMA Brand Green: #2E4A2D)
            $headerRange = "A1:{$lastCol}1";
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E4A2D'],
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
            $sheet->getRowDimension(1)->setRowHeight(28);

            // Write Data Rows
            $rowNum = 2;
            foreach ($config['rows'] as $rowData) {
                foreach ($rowData as $colIdx => $val) {
                    $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . $rowNum;
                    $sheet->setCellValueExplicit($coord, (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                $dataRange = "A{$rowNum}:{$lastCol}{$rowNum}";
                $isEven = ($rowNum % 2 === 0);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'name' => 'Calibri',
                        'color' => ['rgb' => '1F3320'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $isEven ? 'FFFFFF' : 'F9FAF1'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D8DCC3'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($rowNum)->setRowHeight(22);
                $rowNum++;
            }

            // Tambahkan baris petunjuk format CSV di bagian bawah berkas
            $instructions = [
                ['# [PETUNJUK FORMAT]', 'PERHATIAN: Berkas ini WAJIB disimpan dalam format CSV (Comma delimited) (*.csv) sebelum diunggah ke sistem SIMA.'],
                ['# [CARA SIMPAN DI EXCEL]', 'Di Microsoft Excel: Klik File -> Save As -> Pada pilihan Save as type, pilih "CSV (Comma delimited) (*.csv)".'],
                ['# [ATURAN PENGISIAN]', 'Baris 1 adalah Header (JANGAN DIUBAH). Baris yang diawali tanda # akan otomatis diabaikan oleh sistem saat import.'],
            ];

            foreach ($instructions as $insRow) {
                foreach ($insRow as $colIdx => $val) {
                    $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . $rowNum;
                    $sheet->setCellValueExplicit($coord, (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
                $dataRange = "A{$rowNum}:{$lastCol}{$rowNum}";
                $sheet->getStyle($dataRange)->applyFromArray([
                    'font' => [
                        'size' => 9.5,
                        'italic' => true,
                        'name' => 'Calibri',
                        'color' => ['rgb' => '5E6C5A'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F4E3'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D8DCC3'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($rowNum)->setRowHeight(20);
                $rowNum++;
            }

            // Freeze header pane
            $sheet->freezePane('A2');

            // 1. Save .xlsx version
            $xlsxWriter = new Xlsx($spreadsheet);
            foreach ($targetDirs as $dir) {
                $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
                $xlsxWriter->save($filePath);
            }

            // 2. Save .csv version (Standard format used by SIMA controllers)
            $csvFilename = str_replace('.xlsx', '.csv', $filename);
            $csvWriter = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $csvWriter->setDelimiter(',');
            $csvWriter->setEnclosure('"');
            $csvWriter->setLineEnding("\r\n");
            $csvWriter->setSheetIndex(0);
            $csvWriter->setUseBOM(true);

            foreach ($targetDirs as $dir) {
                $csvFilePath = $dir . DIRECTORY_SEPARATOR . $csvFilename;
                $csvWriter->save($csvFilePath);
            }

            $this->line("  ✓ Dibuat: <info>{$csvFilename}</info> & <info>{$filename}</info> (" . count($config['rows']) . " data baris)");
        }

        $this->newLine();
        $this->info("Semua berkas dummy berhasil dibuat di dua lokasi:");
        $this->line("  1. " . base_path('dummy_import_files'));
        $this->line("  2. " . public_path('dummy_import_files'));

        return Command::SUCCESS;
    }
}
