<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

/**
 * Excel Export Class untuk Buku Inventaris Aset Desa.
 *
 * Kolom (10 kolom, A–J):
 *   A = NO
 *   B = KODE BARANG
 *   C = NAMA BARANG
 *   D = NUP
 *   E = MERK / TYPE
 *   F = BUKTI KEPEMILIKAN
 *   G = TAHUN PEROLEHAN
 *   H = UKURAN M²
 *   I = NILAI (Rp.)
 *   J = KET
 */
class BukuInventarisAsetExport extends StringValueBinder implements
    FromCollection,
    WithTitle,
    WithColumnWidths,
    WithEvents,
    WithCustomValueBinder
{
    /** @var array<int, array> Data baris dari BukuInventarisAsetService::buildRows() */
    protected array  $rows;
    protected int    $tahun;
    protected string $periode;
    protected string $tanggalFormatted = '';
    protected bool   $includeSignatureImage;
    protected float  $nilaiTotal = 0.0;

    // Kolom terakhir
    protected string $lastCol = 'J';
    protected int    $totalCols = 10;

    public function __construct(
        array  $rows,
        int    $tahun,
        string $periode   = 'tahunan',
        string $tanggalDokumen = '',
        bool   $includeSignatureImage = true
    ) {
        $this->rows                 = $rows;
        $this->tahun                = $tahun;
        $this->periode              = $periode;
        $this->includeSignatureImage = $includeSignatureImage;
        $this->tanggalFormatted     = $this->formatTanggal($tanggalDokumen);
    }

    // ── WithTitle ──────────────────────────────────────────────────────────────

    public function title(): string
    {
        return 'Buku Inventaris Aset';
    }

    // ── FromCollection ─────────────────────────────────────────────────────────

    public function collection(): Collection
    {
        $collection = collect();
        foreach ($this->rows as $r) {
            $nilai = (float) ($r['nilai'] ?? 0);
            $this->nilaiTotal += $nilai;

            $collection->push([
                $r['no'],
                $r['kode'] ?? '-',
                $r['nama_barang'] ?? '-',
                $r['nup'] ?? '',
                $r['merk_type'] ?? '',
                $r['bukti_kepemilikan'] ?? '-',
                $r['tahun_perolehan'] ?? '',
                $r['ukuran_m2'] ?? '',
                $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '-',
                $r['keterangan'] ?? '',
            ]);
        }

        return $collection;
    }

    // ── WithColumnWidths ───────────────────────────────────────────────────────

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 18,  // KODE BARANG
            'C' => 38,  // NAMA BARANG
            'D' => 7,   // NUP
            'E' => 18,  // MERK / TYPE
            'F' => 18,  // BUKTI KEPEMILIKAN
            'G' => 16,  // TAHUN PEROLEHAN
            'H' => 14,  // UKURAN M²
            'I' => 20,  // NILAI (Rp.)
            'J' => 25,  // KET
        ];
    }

    // ── WithEvents (AfterSheet) ────────────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastCol  = $this->lastCol;

                // Warna header biru muda standard sistem
                $headerBg = '9BC2E6';

                // ── 1. Sisipkan 9 baris di atas data ──────────────────────────
                // Row 1-4: Info lokasi desa di kanan atas (Kolom G-J)
                // Row 5: Spacer
                // Row 6: BUKU INVENTARIS ASET DESA [NAMA] (Merge A6:J6 Centered)
                // Row 7: TAHUN [TAHUN] (PERIODE) (Merge A7:J7 Centered)
                // Row 8: Spacer
                // Row 9: Header kolom teks (NO, KODE BARANG, ...)
                // Data mulai di Row 10 (1 + 9 = 10)
                $sheet->insertNewRowBefore(1, 9);

                $desa          = config('desa');
                $namaLokasi    = strtoupper($desa['nama_lokasi'] ?: ($desa['desa_nama'] ?: 'DEMO'));
                $namaProvinsi  = strtoupper($desa['provinsi_nama'] ?: 'BALI');
                $namaKabupaten = strtoupper($desa['kabupaten_nama'] ?: 'KABUPATEN DEMO');
                $namaKecamatan = strtoupper($desa['kecamatan_nama'] ?: 'KECAMATAN DEMO');
                $namaDesa      = strtoupper($desa['desa_nama'] ?: ($desa['nama_lokasi'] ?: 'DEMO'));

                // ── Baris 1–4: Info lokasi desa di kolom H & I–J (kanan atas) ─────
                $lokasi = [
                    'PROVINSI'   => $namaProvinsi,
                    'KABUPATEN'  => $namaKabupaten,
                    'KECAMATAN'  => $namaKecamatan,
                    'DESA'       => $namaDesa,
                ];
                $lokasiRow = 1;
                foreach ($lokasi as $label => $nilai) {
                    // Kolom H: label (PROVINSI, KABUPATEN, KECAMATAN, DESA)
                    $sheet->setCellValue("H{$lokasiRow}", $label);
                    $sheet->getStyle("H{$lokasiRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Kolom I–J: ": " + nilai (tanda titik dua jadi satu kotak dengan datanya)
                    $sheet->mergeCells("I{$lokasiRow}:J{$lokasiRow}");
                    $sheet->setCellValue("I{$lokasiRow}", ': ' . $nilai);
                    $sheet->getStyle("I{$lokasiRow}:J{$lokasiRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->getRowDimension($lokasiRow)->setRowHeight(15);
                    $lokasiRow++;
                }

                // Baris 5: spacer (1 kotak margin penuh dari text desa dengan judul)
                $sheet->getRowDimension(5)->setRowHeight(16);

                // ── Baris 6–7: Judul utama dan sub-judul (Center di seluruh tabel A–J) ─
                $judulPeriode = match ($this->periode) {
                    'semester_1' => 'SEMESTER I',
                    'semester_2' => 'SEMESTER II',
                    default      => 'TAHUNAN',
                };

                // Baris 6: Judul Utama
                $sheet->mergeCells("A6:{$lastCol}6");
                $sheet->setCellValue('A6', 'BUKU INVENTARIS ASET DESA ' . $namaLokasi);
                $sheet->getStyle('A6')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(20);

                // Baris 7: Sub Judul
                $sheet->mergeCells("A7:{$lastCol}7");
                $sheet->setCellValue('A7', 'TAHUN ' . $this->tahun . ' (' . $judulPeriode . ')');
                $sheet->getStyle('A7')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(18);

                // Baris 8: spacer sebelum header tabel
                $sheet->getRowDimension(8)->setRowHeight(8);

                // ── Baris 9: HEADER KOLOM NAMA ────────────────────────────────
                $headers = [
                    'A' => 'NO',
                    'B' => 'KODE BARANG',
                    'C' => 'NAMA BARANG',
                    'D' => 'NUP',
                    'E' => 'MERK / TYPE',
                    'F' => 'BUKTI KEPEMILIKAN',
                    'G' => 'TAHUN PEROLEHAN',
                    'H' => "UKURAN M\u{00B2}",
                    'I' => 'NILAI (Rp.)',
                    'J' => 'KET',
                ];
                foreach ($headers as $col => $label) {
                    $sheet->setCellValue("{$col}9", $label);
                }
                $sheet->getStyle("A9:{$lastCol}9")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerBg]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(9)->setRowHeight(26);

                // ── 2. Style baris data ───────────────────────────────────────
                $dataStart = 10;
                $lastRow   = $sheet->getHighestRow();

                if ($lastRow >= $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['size' => 9],
                    ]);

                    for ($r = $dataStart; $r <= $lastRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1); // auto height
                    }

                    // Format Center: NO, KODE BARANG, NUP, BUKTI, TAHUN, UKURAN
                    foreach (['A', 'B', 'D', 'F', 'G', 'H'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Format Text untuk Kode Barang dan NUP (mencegah format scientific)
                    $sheet->getStyle("B{$dataStart}:B{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle("D{$dataStart}:D{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);

                    // Format NILAI → rata kanan & format text untuk menjaga separator titik Indonesia
                    $sheet->getStyle("I{$dataStart}:I{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle("I{$dataStart}:I{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Format Wrap Text: NAMA BARANG, MERK / TYPE, KET
                    foreach (['C', 'E', 'J'] as $col) {
                        $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                            ->setVertical(Alignment::VERTICAL_CENTER)
                            ->setWrapText(true);
                    }
                }

                // ── 3. Baris NILAI ASET (total) ───────────────────────────────
                $totalRow = ($lastRow >= $dataStart) ? $lastRow + 1 : $dataStart + 1;

                $sheet->mergeCells("A{$totalRow}:H{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'NILAI ASET');
                $sheet->setCellValue("I{$totalRow}", number_format($this->nilaiTotal, fmod($this->nilaiTotal, 1) != 0 ? 2 : 0, ',', '.'));
                $sheet->getStyle("I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'font'      => ['bold' => true, 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerBg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($totalRow)->setRowHeight(20);

                // ── 4. Tanda Tangan Resmi (Hanya Perbekel di Sisi Kanan) ───────
                $perbekel = $this->resolvePerbekel();

                if ($perbekel) {
                    $rowCursor = $totalRow + 2;

                    // Baris 1 TTD: Tanggal Dokumen di Sisi Kanan (G..J)
                    if (!empty($this->tanggalFormatted)) {
                        $sheet->mergeCells("G{$rowCursor}:J{$rowCursor}");
                        $sheet->setCellValue("G{$rowCursor}", $this->tanggalFormatted);
                        $sheet->getStyle("G{$rowCursor}:J{$rowCursor}")->applyFromArray([
                            'font'      => ['size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($rowCursor)->setRowHeight(16);
                        $rowCursor++;
                    }

                    // Baris 2 TTD: Jabatan Perbekel
                    $jabatanPerbekel = trim((string)($perbekel['jabatan'] ?? 'Perbekel ' . ucwords(strtolower($namaLokasi))));
                    $sheet->mergeCells("G{$rowCursor}:J{$rowCursor}");
                    $sheet->setCellValue("G{$rowCursor}", $jabatanPerbekel . ',');
                    $sheet->getStyle("G{$rowCursor}:J{$rowCursor}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);
                    $sheet->getRowDimension($rowCursor)->setRowHeight(18);
                    $rowCursor++;

                    // Baris 3 TTD: Ruang TTD / Gambar TTD
                    $ttdRowH = 55;
                    $sheet->getRowDimension($rowCursor)->setRowHeight($ttdRowH);

                    // Gambar TTD Perbekel jika ada
                    if ($this->includeSignatureImage) {
                        $rawPath = trim((string)($perbekel['ttd_path'] ?? ''));
                        $ttdFull = $rawPath !== '' ? storage_path('app/public/' . ltrim($rawPath, '/')) : '';
                        if ($ttdFull !== '' && file_exists($ttdFull)) {
                            $drawing = new Drawing();
                            $drawing->setName('TTD_Perbekel');
                            $drawing->setPath($ttdFull);
                            $drawing->setCoordinates("H{$rowCursor}");
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(4);
                            $drawing->setHeight(48);
                            $drawing->setWorksheet($sheet);
                        }
                    }
                    $rowCursor++;

                    // Baris 4 TTD: Nama Pejabat (Underline + Bold)
                    $namaP = trim((string)($perbekel['nama'] ?? ''));
                    $sheet->mergeCells("G{$rowCursor}:J{$rowCursor}");
                    $sheet->setCellValue("G{$rowCursor}", $namaP);
                    $sheet->getStyle("G{$rowCursor}:J{$rowCursor}")->applyFromArray([
                        'font'      => ['size' => 9, 'bold' => true, 'underline' => Font::UNDERLINE_SINGLE],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($rowCursor)->setRowHeight(20);
                }

                // ── 5. Print settings ─────────────────────────────────────────
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()
                    ->setTop(0.4)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);

                // Header/footer untuk halaman
                $sheet->getHeaderFooter()
                    ->setOddFooter('&L&8Buku Inventaris Aset Desa&R&8Halaman &P dari &N');
            },
        ];
    }

    // ── HELPERS ────────────────────────────────────────────────────────────────

    /**
     * Format tanggal ke "Demo, 31 Desember 2025".
     */
    protected function formatTanggal(string $tanggal): string
    {
        if (empty($tanggal)) {
            return '';
        }
        try {
            $dt    = \Carbon\Carbon::parse($tanggal);
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $namaLokasi = config('desa.nama_lokasi', 'Demo');
            $tglStr     = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            return $namaLokasi !== '' ? $namaLokasi . ', ' . $tglStr : $tglStr;
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Cari data Perbekel untuk TTD.
     */
    protected function resolvePerbekel(): ?array
    {
        $userPerbekel = \App\Models\User::with(['role', 'pejabat'])
            ->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))
            ->where('is_aktif', 1)
            ->first();

        if ($userPerbekel) {
            return [
                'nama'     => $userPerbekel->pejabat?->nama ?? $userPerbekel->nama,
                'jabatan'  => 'Perbekel ' . ucwords(strtolower(config('desa.nama_lokasi', 'Demo'))),
                'ttd_path' => $userPerbekel->pejabat?->ttd_path ?? null,
            ];
        }

        $pejabat = \App\Models\Pejabat::where(function ($q) {
            $q->where('jabatan', 'LIKE', '%perbekel%')
              ->orWhere('jabatan', 'LIKE', '%kepala desa%');
        })->first();

        if ($pejabat) {
            return [
                'nama'     => $pejabat->nama,
                'jabatan'  => $pejabat->jabatan,
                'ttd_path' => $pejabat->ttd_path ?? null,
            ];
        }

        return null;
    }

    /**
     * Cari data Kaur TU / Pengelola Aset untuk TTD.
     */
    protected function resolveKaurTU(): ?array
    {
        $userKaurTU = \App\Models\User::with(['role', 'bidang', 'pejabat'])
            ->whereHas('role', fn($q) => $q->where('nama', 'kaur'))
            ->whereHas('bidang', fn($q) => $q->where('nama', 'LIKE', '%tata usaha%')
                                             ->orWhere('nama', 'LIKE', '%umum%'))
            ->where('is_aktif', 1)
            ->first();

        if ($userKaurTU) {
            return [
                'nama'     => $userKaurTU->pejabat?->nama ?? $userKaurTU->nama,
                'jabatan'  => 'Pengelola Aset ( Kaur Umum)',
                'ttd_path' => $userKaurTU->pejabat?->ttd_path ?? null,
            ];
        }

        $pejabatKaurTU = \App\Models\Pejabat::where('jabatan', 'LIKE', '%kaur%')
            ->where(function ($q) {
                $q->where('jabatan', 'LIKE', '%tata usaha%')
                  ->orWhere('jabatan', 'LIKE', '%umum%');
            })
            ->first();

        if ($pejabatKaurTU) {
            return [
                'nama'     => $pejabatKaurTU->nama,
                'jabatan'  => $pejabatKaurTU->jabatan,
                'ttd_path' => $pejabatKaurTU->ttd_path ?? null,
            ];
        }

        return null;
    }
}
