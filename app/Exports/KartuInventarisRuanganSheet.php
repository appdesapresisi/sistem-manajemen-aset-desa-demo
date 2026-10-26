<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class KartuInventarisRuanganSheet extends StringValueBinder implements
    FromArray,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents,
    WithCustomValueBinder
{
    protected array $rows = [];
    protected int   $headerEndRow   = 0;
    protected int   $dataStartRow   = 0;
    protected int   $jumlahRow      = 0;
    protected array $ruanganRowNums = [];
    protected int   $ttdRow         = 0;

    public function __construct(
        protected array  $ruanganData,
        protected float  $totalNilai,
        protected ?array $sekretarisPejabat,
        protected array  $petugasPejabat,
        protected bool   $includeImg,
        protected array  $filters = [],
        protected string $tanggalDokumen = '',
        protected string $keteranganDokumen = ''
    ) {
        $this->buildRows();
    }

    protected function buildRows(): void
    {
        $desa = config('desa');
        $rows = [];

        // Row 1: Judul
        $rows[] = ['KARTU INVENTARIS RUANGAN', '', '', '', '', '', '', '', '', ''];

        // Row 2: kosong
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        // Rows 3-8: Info desa — A = "Label           :" (str_pad agar ':' sejajar), B = nilai, merge B:J
        // Label terpanjang: "No. Kode Lokasi" = 15 karakter → pad semua ke 15
        $pad = 15;
        $rows[] = [str_pad('Provinsi',        $pad) . ' :', $desa['provinsi_kode'] . '. ' . $desa['provinsi_nama'], '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('Kab./Kota',       $pad) . ' :', $desa['kabupaten_kode'] . '. ' . $desa['kabupaten_nama'], '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('Kecamatan',       $pad) . ' :', $desa['kecamatan_kode'] . '. ' . $desa['kecamatan_nama'], '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('Desa',            $pad) . ' :', $desa['kode_lokasi'] . '. ' . $desa['desa_nama'], '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('UPB',             $pad) . ' :', $desa['upb'], '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('No. Kode Lokasi', $pad) . ' :', $desa['kode_lokasi'] . '.', '', '', '', '', '', '', '', ''];

        // Row 9: kosong
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        // Row 10-11: Header tabel (2 baris)
        $rows[] = ['NO', 'JENIS BARANG / NAMA BARANG', 'N O M O R', '', 'TAHUN PEROLEHAN', 'I D E N T I T A S', '', 'ASAL-USUL BARANG', 'HARGA (Ribuan) Rp', 'KETERANGAN'];
        $rows[] = ['', '', 'KODE BARANG', 'REG', '', 'KONSTRUKSI/LUAS/MERK/TYPE/WARNA/JUDUL/PENCIPTA', 'NO. POL', '', '', ''];

        // Row 12: Nomor kolom
        $rows[] = ['1', '2', '3', '4', '5', '6', '', '7', '8', '9'];

        $this->headerEndRow = count($rows);
        $this->dataStartRow = count($rows) + 1;

        $this->totalNilai = 0.0;

        // Data rows
        foreach ($this->ruanganData as $rd) {
            $ruangan = $rd['ruangan'];
            $rdRows  = $rd['rows'];

            if (empty($rdRows)) continue;

            // Baris separator ruangan
            $this->ruanganRowNums[] = count($rows) + 1;
            $rows[] = ['Nama Ruangan : ' . $ruangan->kode . ' .' . $ruangan->nama, '', '', '', '', '', '', '', '', ''];

            foreach ($rdRows as $row) {
                // Ambil nilai perolehan langsung dari objek inventaris jika tersedia, atau parse harga
                $nilai = isset($row['inv_obj']) && isset($row['inv_obj']->nilai_perolehan)
                    ? (float)$row['inv_obj']->nilai_perolehan
                    : 0.0;
                if ($nilai <= 0 && !empty($row['harga'])) {
                    $cleaned = str_replace(['.', ','], ['', '.'], (string)$row['harga']);
                    $nilai = is_numeric($cleaned) ? (float)$cleaned : 0.0;
                }
                $this->totalNilai += $nilai;
                $hargaFormatted = $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '';
                
                $rows[] = [
                    $row['no'],
                    $row['nama_barang'],
                    $row['kode_barang'],
                    $row['nup'],
                    $row['tahun'],
                    $row['identitas'],
                    $row['no_pol'],
                    $row['asal_usul'],
                    $hargaFormatted,
                    $row['keterangan'],
                ];
            }
        }

        // Baris JUMLAH HARGA
        $this->jumlahRow = count($rows) + 1;
        $rows[] = ['JUMLAH HARGA', '', '', '', '', '', '', '', number_format($this->totalNilai, fmod($this->totalNilai, 1) != 0 ? 2 : 0, ',', '.'), ''];

        // Baris kosong sebelum TTD
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        // Resolve tanggal: gunakan input user jika ada, fallback ke hari ini
        $namaLokasi = $this->keteranganDokumen ?: config('desa.nama_lokasi', '');
        if (!empty($this->tanggalDokumen)) {
            try {
                $dt     = \Carbon\Carbon::parse($this->tanggalDokumen);
                $bulan  = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                           7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                $tglStr = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {
                $tglStr = date('d') . ' ' . ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][date('n')] . ' ' . date('Y');
            }
        } else {
            $tglStr = date('d') . ' ' . ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][date('n')] . ' ' . date('Y');
        }
        $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi . ', ' : '') . $tglStr;

        $sekNama    = trim((string)($this->sekretarisPejabat['nama'] ?? ''));
        $petugasNama = !empty($this->petugasPejabat) ? trim((string)($this->petugasPejabat[0]['nama'] ?? '')) : '';

        if ($sekNama !== '' || $petugasNama !== '') {
            // TTD rows
            $this->ttdRow = count($rows) + 1;

            $rows[] = ['MENGETAHUI', '', '', '', '', $tanggalDisplay, '', '', '', ''];
            $rows[] = ['SEKRETARIS DESA', '', '', '', '', 'PETUGAS / PENGURUS BARANG MILIK DESA', '', '', '', ''];
            $rows[] = ['Selaku Pembantu Pengelola Barang Milik Desa', '', '', '', '', '', '', '', '', ''];
            $rows[] = ['', '', '', '', '', '', '', '', '', ''];
            $rows[] = ['', '', '', '', '', '', '', '', '', ''];
            $rows[] = ['', '', '', '', '', '', '', '', '', ''];
            $rows[] = [$sekNama, '', '', '', '', $petugasNama, '', '', '', ''];
        } else {
            $this->ttdRow = 0;
        }

        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Kartu Inventaris Ruangan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // cukup untuk "No. Kode Lokasi :" + kolom NO tabel
            'B' => 22,  // JENIS BARANG / NAMA BARANG
            'C' => 18,  // KODE BARANG
            'D' => 8,   // REG
            'E' => 8,   // TAHUN
            'F' => 32,  // IDENTITAS
            'G' => 8,   // NO. POL
            'H' => 14,  // ASAL-USUL
            'I' => 22,  // HARGA (diperlebar ke 22 untuk menampung angka miliaran dengan format)
            'J' => 14,  // KETERANGAN
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalRows = count($this->rows);

                // Judul — merge A1:J1, bold, center, large
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Info desa rows 3-8: A = "Label :" (str_pad), B = nilai (merge B:J), bold nilai
                for ($r = 3; $r <= 8; $r++) {
                    $sheet->getStyle('B' . $r)->getFont()->setBold(true);
                    $sheet->mergeCells('B' . $r . ':J' . $r);
                    // Font monospace agar str_pad bekerja dengan baik
                    $sheet->getStyle('A' . $r)->getFont()->setName('Courier New')->setSize(9);
                }

                // Header tabel row 10-11
                $h1 = $this->headerEndRow - 2; // row 10
                $h2 = $this->headerEndRow - 1; // row 11
                $h3 = $this->headerEndRow;     // row 12 (nomor kolom)

                // Merge header row 1
                $sheet->mergeCells('A' . $h1 . ':A' . $h2); // NO
                $sheet->mergeCells('B' . $h1 . ':B' . $h2); // JENIS BARANG
                $sheet->mergeCells('C' . $h1 . ':D' . $h1); // NOMOR
                $sheet->mergeCells('E' . $h1 . ':E' . $h2); // TAHUN
                $sheet->mergeCells('F' . $h1 . ':G' . $h1); // IDENTITAS
                $sheet->mergeCells('H' . $h1 . ':H' . $h2); // ASAL-USUL
                $sheet->mergeCells('I' . $h1 . ':I' . $h2); // HARGA
                $sheet->mergeCells('J' . $h1 . ':J' . $h2); // KETERANGAN

                // Nomor kolom row: merge F:G
                $sheet->mergeCells('F' . $h3 . ':G' . $h3);

                // Style header
                $headerRange = 'A' . $h1 . ':J' . $h3;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders'   => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                $sheet->getRowDimension($h1)->setRowHeight(20);
                $sheet->getRowDimension($h2)->setRowHeight(20);
                $sheet->getRowDimension($h3)->setRowHeight(12);

                // Data rows — border + wrap
                if ($this->dataStartRow <= $this->jumlahRow) {
                    $dataRange = 'A' . $this->dataStartRow . ':J' . $this->jumlahRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font'      => ['size' => 8],
                        'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    
                    // Alignment untuk kolom-kolom data ANGKA (CENTER) - sesuai PDF
                    // Kolom A (NO), C (KODE BARANG), D (REG/NUP), E (TAHUN), F (IDENTITAS), G (NO. POL) = CENTER
                    $sheet->getStyle('A' . $this->dataStartRow . ':A' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C' . $this->dataStartRow . ':C' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $this->dataStartRow . ':D' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $this->dataStartRow . ':E' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $this->dataStartRow . ':F' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('G' . $this->dataStartRow . ':G' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Kolom I (HARGA) = RIGHT - sesuai PDF
                    $sheet->getStyle('I' . $this->dataStartRow . ':I' . $this->jumlahRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    
                    // Format kolom HARGA sebagai NUMBER dengan format Indonesia (titik = ribuan, koma = desimal)
                    // Format: #,##0 → 18.500.000
                    $sheet->getStyle('I' . $this->dataStartRow . ':I' . $this->jumlahRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0_);[Red](#,##0)');
                }

                // Ruangan separator rows — abu-abu, bold, italic, merge A:J, LEFT alignment (sesuai PDF)
                foreach ($this->ruanganRowNums as $rn) {
                    $sheet->mergeCells('A' . $rn . ':J' . $rn);
                    $sheet->getStyle('A' . $rn . ':J' . $rn)->applyFromArray([
                        'font'      => ['bold' => true, 'italic' => true, 'size' => 8],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                // Jumlah harga row — merge A:H, bold, center untuk text, RIGHT untuk nilai
                $sheet->mergeCells('A' . $this->jumlahRow . ':H' . $this->jumlahRow);
                $sheet->getStyle('A' . $this->jumlahRow . ':J' . $this->jumlahRow)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                // Kolom I (nilai jumlah harga) = RIGHT - sesuai PDF
                $sheet->getStyle('I' . $this->jumlahRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // TTD rows — merge kiri A:E, kanan F:J
                if ($this->ttdRow > 0) {
                    for ($r = $this->ttdRow; $r <= $this->ttdRow + 6; $r++) {
                        $sheet->mergeCells('A' . $r . ':E' . $r);
                        $sheet->mergeCells('F' . $r . ':J' . $r);
                        $sheet->getStyle('A' . $r)->applyFromArray([
                            'font'      => ['bold' => false, 'size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $sheet->getStyle('F' . $r)->applyFromArray([
                            'font'      => ['bold' => false, 'size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }

                    // Bold nama pejabat (baris terakhir TTD)
                    $namaRow = $this->ttdRow + 6;
                    $sheet->getStyle('A' . $namaRow)->getFont()->setBold(true)->setUnderline(true);
                    $sheet->getStyle('F' . $namaRow)->getFont()->setBold(true)->setUnderline(true);
                }

                // Landscape orientation
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                // Row heights untuk data
                for ($r = $this->dataStartRow; $r <= $this->jumlahRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(14);
                }
            },
        ];
    }
}
