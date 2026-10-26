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
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class KartuInventarisRuanganFormat2Sheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $rows = [];
    protected int   $headerEndRow  = 0;
    protected int   $dataStartRow  = 0;
    protected int   $jumlahRow     = 0;
    protected array $ruanganRowNums = [];
    protected int   $ttdRow        = 0;

    public function __construct(
        protected array  $ruanganData,
        protected float  $totalNilai,
        protected ?array $perbekelPejabat,
        protected ?array $kasiKesejahteraan,
        protected array  $petugasPejabat,
        protected bool   $includeImg,
        protected array  $filters = [],
        protected string $tanggalDokumen = '',
        protected string $keteranganDokumen = ''
    ) {
        $this->buildRows();
    }

    public function title(): string
    {
        if (count($this->ruanganData) === 1) {
            $name = $this->ruanganData[0]['ruangan']->nama ?? 'KIR';
            // Bersihkan nama sheet dari karakter ilegal: \ / ? * : [ ] dan batasi 30 karakter
            $cleanName = preg_replace('/[*?:\\\\\/\[\]]/', '', $name);
            return mb_substr($cleanName, 0, 30);
        }
        return 'Kartu Inventaris Ruangan';
    }

    protected function buildRows(): void
    {
        $desa = config('desa');
        $tahunFilter = (int)($this->filters['tahun'] ?? date('Y'));
        if (!$tahunFilter) $tahunFilter = (int)date('Y');

        $rows = [];

        // Row 1: Judul
        $rows[] = ['KARTU INVENTARIS RUANGAN TAHUN ' . $tahunFilter, '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', '', '', '', ''];

        // Rows 3–6: Info desa (pad ke 15 char) — kolom A label, B nilai
        $pad = 15;
        $rows[] = [str_pad('PROVINSI',     $pad) . ' :', $desa['provinsi_nama'] ?? '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('KABUPATEN',    $pad) . ' :', $desa['kabupaten_nama'] ?? '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = [str_pad('SATUAN KERJA', $pad) . ' :', $desa['upb'] ?? '', '', '', '', '', '', '', '', '', '', '', ''];

        // Nama ruangan — hanya dari ruangan yang punya data, uppercase untuk konsistensi tampilan dokumen
        $namaRuangans = strtoupper(
            collect($this->ruanganData)
                ->filter(fn($rd) => !empty($rd['rows']))
                ->map(fn($rd) => trim($rd['ruangan']->nama))
                ->filter()
                ->implode(', ')
        );
        // Row 6: RUANGAN (kiri) + NO. KODE LOKASI sejajar di kolom K–M (kanan) — baris yang sama
        $rows[] = [str_pad('RUANGAN', $pad) . ' :', $namaRuangans, '', '', '', '', '', '', '', '', 'NO. KODE LOKASI : ' . ($desa['kode_lokasi'] ?? ''), '', ''];

        // Row 7–8: Header tabel 2 baris (langsung setelah info, tidak ada row kosong)
        $rows[] = ['NO', 'JENIS BARANG/ NAMA BARANG', 'MERK/ MODEL', 'NO. SERI PABRIK', 'UKURAN (P x L x T)', 'BAHAN', 'THN. PEMBUATAN/ PEMBELIAN', 'NO. KODE BARANG', 'JUMLAH', 'KEADAAN BARANG', '', '', 'KET.'];
        $rows[] = ['', '', '', '', '', '', '', '', '', 'B', 'KB', 'RB', ''];

        // Row 9: Nomor kolom
        $rows[] = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13'];

        $this->headerEndRow = count($rows);
        $this->dataStartRow = count($rows) + 1;

        // Data rows — Format 2 tidak pakai separator "Nama Ruangan" di dalam tabel
        foreach ($this->ruanganData as $rd) {
            $ruangan = $rd['ruangan'];
            $rdRows  = $rd['rows'];
            if (empty($rdRows)) continue;

            foreach ($rdRows as $row) {
                $inv = $row['inv_obj'] ?? null;

                $merkModel  = $inv ? trim((string)($inv->getKirValue('kir_merk_tipe') ?? '')) : '';
                $noSeri     = $inv ? trim((string)($inv->getKirValue('kir_no_seri_pabrik') ?? '')) : '';
                $panjang    = $inv ? trim((string)($inv->getKirValue('kir_panjang') ?? '')) : '';
                $lebar      = $inv ? trim((string)($inv->getKirValue('kir_lebar') ?? '')) : '';
                $tinggi     = $inv ? trim((string)($inv->getKirValue('kir_tinggi') ?? '')) : '';
                $bahan      = $inv ? trim((string)($inv->getKirValue('kir_bahan') ?? '')) : '';
                $kirKondisi = $inv ? trim((string)($inv->getKirValue('kir_kondisi') ?? ($inv->kondisi ?? ''))) : '';

                $ukuranParts = array_filter([$panjang, $lebar, $tinggi]);
                $ukuran = implode(' x ', $ukuranParts);

                $kondisiNorm = strtolower(str_replace(' ', '_', $kirKondisi));
                $isB  = in_array($kondisiNorm, ['baik', 'b'])                       ? 'B'  : '';
                $isKB = in_array($kondisiNorm, ['rusak_ringan', 'kb', 'kurang_baik']) ? 'KB' : '';
                $isRB = in_array($kondisiNorm, ['rusak_berat', 'rb'])                 ? 'RB' : '';

                $rows[] = [
                    $row['no'],
                    $row['nama_barang'],
                    $merkModel ?: '-',
                    $noSeri    ?: '-',
                    $ukuran    ?: '-',
                    $bahan     ?: '-',
                    $row['tahun'] ?? '-',
                    $row['kode_barang'],
                    1,         // jumlah default
                    $isB,
                    $isKB,
                    $isRB,
                    $row['keterangan'] ?? '',
                ];
            }
        }

        // Tidak ada row JUMLAH di Format 2
        $this->jumlahRow = count($rows) + 1; // tetap dipakai sebagai batas data

        // TTD rows
        $this->ttdRow = count($rows) + 3;

        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            // A: tidak di-set, pakai autoSize di registerEvents
            'B' => 22,  // JENIS BARANG/NAMA
            'C' => 14,  // MERK/MODEL
            'D' => 12,  // NO.SERI PABRIK
            'E' => 18,  // UKURAN
            'F' => 10,  // BAHAN
            'G' => 10,  // TAHUN
            'H' => 16,  // KODE BARANG
            'I' => 6,   // JUMLAH
            'J' => 5,   // B
            'K' => 5,   // KB
            'L' => 5,   // RB
            'M' => 14,  // KET
        ];
    }

    public function styles($sheet): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastCol  = 'M';
                $totalCols = 13;

                // ── Row 1: Judul ──────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // ── Rows 3–6: Info desa, kolom A monospace, B bold ─────────
                for ($r = 3; $r <= 5; $r++) {
                    $sheet->getStyle("A{$r}")->getFont()->setName('Courier New')->setSize(9);
                    $sheet->getStyle("B{$r}")->getFont()->setBold(true)->setSize(10);
                    $sheet->mergeCells("B{$r}:J{$r}");
                }

                // ── Row 6: RUANGAN (kiri) + NO. KODE LOKASI (kanan, sejajar) ──
                $sheet->getStyle('A6')->getFont()->setName('Courier New')->setSize(9);
                $sheet->getStyle('B6')->getFont()->setBold(true)->setSize(10);
                $sheet->mergeCells('B6:J6');
                // NO. KODE LOKASI di K6:M6 — sejajar dengan baris RUANGAN
                $sheet->mergeCells('K6:M6');
                $sheet->getStyle('K6')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // ── Row 7–8: Header tabel (langsung setelah info, tidak ada spacer) ──
                $noRowspan = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'M'];
                foreach ($noRowspan as $col) {
                    $sheet->mergeCells("{$col}7:{$col}8");
                }
                $sheet->mergeCells('J7:L7'); // KEADAAN BARANG colspan=3

                $sheet->getStyle("A7:{$lastCol}8")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(36);
                $sheet->getRowDimension(8)->setRowHeight(20);

                // ── Row 9: Nomor kolom ────────────────────────────────────
                $sheet->getStyle("A9:{$lastCol}9")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(9)->setRowHeight(14);

                $dataStart = $this->dataStartRow;
                $lastRow   = $this->jumlahRow - 1;

                // ── Data rows styling ──────────────────────────────────────
                if ($lastRow >= $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['size' => 9],
                        'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                    ]);

                    // (Format 2 tidak punya separator row ruangan di dalam tabel)

                    // Center: NO, kondisi B/KB/RB
                    $sheet->getStyle("A{$dataStart}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("J{$dataStart}:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$dataStart}:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$dataStart}:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$dataStart}:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── TTD 3 kolom: Perbekel | Kasi Kesejahteraan | Pengurus Barang ──
                $perbekelNama = trim((string)($this->perbekelPejabat['nama'] ?? ''));
                $kasiNama     = trim((string)($this->kasiKesejahteraan['nama'] ?? ''));
                $petugasNama  = !empty($this->petugasPejabat) ? trim((string)($this->petugasPejabat[0]['nama'] ?? '')) : '';
                
                $showPerbekel = ($perbekelNama !== '');
                $showKasi     = ($kasiNama !== '');
                $showPetugas  = ($petugasNama !== '');
                
                $jRow = $this->jumlahRow;
                if ($showPerbekel || $showKasi || $showPetugas) {
                    $ttdStartRow = $jRow + 2;

                    // Tanggal
                    $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    $namaLokasi = $this->keteranganDokumen ?: config('desa.nama_lokasi', '');
                    if ($this->tanggalDokumen) {
                        try { $dt = \Carbon\Carbon::parse($this->tanggalDokumen); $tglStr = $dt->day.' '.$bulan[$dt->month].' '.$dt->year; }
                        catch (\Throwable $e) { $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y'); }
                    } else {
                        $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y');
                    }
                    $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi.', ' : '').$tglStr;

                    // Kolom TTD: A:D | E:I | J:M
                    $ttdCols    = [['A','D'], ['E','I'], ['J','M']];
                    $ttdPejabats = [$showPerbekel ? $this->perbekelPejabat : null, $showKasi ? $this->kasiKesejahteraan : null, $showPetugas ? ($this->petugasPejabat[0] ?? null) : null];
                    $ttdHeaders  = ['Mengetahui', 'Penanggungjawab,', $tanggalDisplay];

                    // Label jabatan ikut dari data pejabat yang dipilih (bukan hardcode)
                    $kasiJabatan    = !empty($this->kasiKesejahteraan['jabatan'])
                        ? $this->kasiKesejahteraan['jabatan']
                        : 'Penanggung Jawab';
                    $petugasJabatan = !empty($this->petugasPejabat[0]['jabatan'])
                        ? $this->petugasPejabat[0]['jabatan']
                        : 'Pengurus Barang ' . (config('desa.nama_lokasi') ?: 'Desa');

                    $perbekelJabatan = !empty($this->perbekelPejabat['jabatan'])
                        ? $this->perbekelPejabat['jabatan'] . ','
                        : 'Perbekel ' . (config('desa.nama_lokasi') ?: 'Desa') . ',';

                    $ttdSubs = [$perbekelJabatan, $kasiJabatan.',', $petugasJabatan.','];

                    $r = $ttdStartRow;
                    foreach ($ttdCols as $i => [$cs, $ce]) {
                        // Skip kolom jika data pejabat kosong — tidak tampilkan apapun
                        $p = $ttdPejabats[$i];
                        if (!$p) continue;

                        // Header baris 1
                        $sheet->mergeCells("{$cs}{$r}:{$ce}{$r}");
                        $sheet->setCellValue("{$cs}{$r}", $ttdHeaders[$i]);
                        $sheet->getStyle("{$cs}{$r}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        // Sub-header baris 2
                        $r2 = $r + 1;
                        $sheet->mergeCells("{$cs}{$r2}:{$ce}{$r2}");
                        $sheet->setCellValue("{$cs}{$r2}", $ttdSubs[$i]);
                        $sheet->getStyle("{$cs}{$r2}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 9],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);

                        // Ruang TTD
                        $r3 = $r2 + 1;
                        $sheet->mergeCells("{$cs}{$r3}:{$ce}{$r3}");
                        $sheet->getRowDimension($r3)->setRowHeight(55);

                        // Gambar TTD
                        if ($this->includeImg && !empty($p['ttd_path'])) {
                            $full = storage_path('app/public/'.ltrim($p['ttd_path'],'/'));
                            if (file_exists($full)) {
                                $drawing = new Drawing();
                                $drawing->setPath($full);
                                $drawing->setCoordinates("{$cs}{$r3}");
                                $drawing->setOffsetX(10);
                                $drawing->setOffsetY(5);
                                $drawing->setWidth(120);
                                $drawing->setHeight(48);
                                $drawing->setResizeProportional(true);
                                $drawing->setWorksheet($sheet);
                            }
                        }

                        // Nama (underline)
                        $r4 = $r3 + 1;
                        $sheet->mergeCells("{$cs}{$r4}:{$ce}{$r4}");
                        $sheet->setCellValue("{$cs}{$r4}", $p ? ($p['nama'] ?? '') : '');
                        $sheet->getStyle("{$cs}{$r4}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 9, 'underline' => Font::UNDERLINE_SINGLE],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $sheet->getRowDimension($r4)->setRowHeight(18);
                    }
                }

                // ── Kolom A: auto-width agar label info desa terbaca ─────
                $sheet->getColumnDimension('A')->setAutoSize(true);

                // ── Print settings ─────────────────────────────────────────
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
            },
        ];
    }
}
