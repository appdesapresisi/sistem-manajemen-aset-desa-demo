<?php

namespace App\Exports\Kib;

use App\Models\Inventaris;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export satu sheet KIB ke Excel.
 * Kolom dan format mengikuti standar Permendagri per KIB type.
 */
class KibSheetExport implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    protected string $kibType;
    protected array $kibConfig;
    protected Collection $inventarisList;
    protected ?array $pejabatKiri;
    protected ?array $pejabatKanan;
    protected bool $includeImg;
    protected string $tanggalDokumen;
    protected string $keteranganDokumen;
    protected array $filters;
    protected float $totalHarga = 0.0;
    protected int $rowCounter = 0;

    public function __construct(
        string $kibType, array $kibConfig, Collection $inventarisList,
        ?array $pejabatKiri, ?array $pejabatKanan, bool $includeImg,
        string $tanggalDokumen, string $keteranganDokumen, array $filters
    ) {
        $this->kibType           = $kibType;
        $this->kibConfig         = $kibConfig;
        $this->inventarisList    = $inventarisList;
        $this->pejabatKiri       = $pejabatKiri;
        $this->pejabatKanan      = $pejabatKanan;
        $this->includeImg        = $includeImg;
        $this->tanggalDokumen    = $tanggalDokumen;
        $this->keteranganDokumen = $keteranganDokumen;
        $this->filters           = $filters;
    }

    public function title(): string
    {
        return 'KIB ' . $this->kibType . ' - ' . ($this->kibConfig['nama'] ?? '');
    }

    public function collection(): Collection
    {
        $rows = collect();
        foreach ($this->inventarisList as $inv) {
            $nilai = (float)($inv->nilai_perolehan ?? 0);
            $this->totalHarga += $nilai;
            $rows->push($this->buildRow($inv, $nilai));
        }
        return $rows;
    }

    protected function buildRow(Inventaris $inv, float $nilai): array
    {
        $emptyDash = fn($val) => ($val === null || trim((string)$val) === '') ? '-' : (string)$val;

        $kv = function(string $key) use ($inv): string {
            $val = $inv->getKibValue($key);
            if ($val === null || trim((string)$val) === '') {
                return '-';
            }
            return (string)$val;
        };
        $kvNum = function(string $key) use ($inv): string {
            $v = $inv->getKibValue($key);
            if ($v === null || trim((string)$v) === '') {
                return '-';
            }
            return number_format((float)$v, 2, ',', '.');
        };
        $kondisiMap = ['baik' => 'B', 'rusak_ringan' => 'RR', 'rusak_berat' => 'RB'];
        $kondisi = $kondisiMap[$inv->kondisi ?? ''] ?? '';
        $asalUsulMap = ['apbdes' => 'APBDesa', 'perolehan_lain' => 'Perolehan Lain', 'kekayaan_asli' => 'Kekayaan Asli'];
        $asalUsul = $asalUsulMap[$inv->asal_usul ?? ''] ?? ($inv->asal_usul ?? '');
        $harga = $nilai > 0 ? number_format($nilai, 2, ',', '.') : '';

        $base = [
            ++$this->rowCounter,
            $emptyDash($inv->display_name ?? $inv->nama_barang),
            $emptyDash($inv->kode_barang),
            $emptyDash($inv->nup),
        ];

        $extra = match($this->kibType) {
            'A' => [$kvNum('luas_m2'), $emptyDash($inv->tahun_perolehan), $kv('tahun_buku'), $kv('alamat'), $kv('hak'), $kv('sertifikat_nomor'), $kv('sertifikat_tgl')],
            'B' => [$kv('merk_type'), $kv('ukuran_cc'), $kv('bahan'), $emptyDash($inv->tahun_perolehan), $kv('tahun_buku'), $kv('no_pabrik'), $kv('no_rangka'), $kv('no_mesin'), $kv('no_polisi'), $kv('no_bpkb')],
            'C' => [$emptyDash($kondisi), $kv('konstruksi_bertingkat'), $kv('konstruksi_beton'), $kvNum('luas_m2'), $emptyDash($inv->tahun_perolehan), $kv('tahun_buku'), $kv('dokumen_tgl'), $kv('dokumen_nomor'), $kv('alamat'), $kv('status_tanah'), $kv('nomor_kode_tanah')],
            'D' => [$kv('konstruksi'), $kvNum('panjang_km'), $kvNum('lebar_m'), $kvNum('luas_m2'), $emptyDash($inv->tahun_perolehan), $kv('tahun_buku'), $kv('dokumen_tgl'), $kv('dokumen_nomor'), $kv('alamat'), $kv('tanah_status'), $kv('tanah_kode')],
            'E' => [$kv('judul_pencipta'), $kv('spesifikasi'), $kv('asal_daerah'), $kv('pencipta_kesenian'), $kv('bahan'), $kv('jenis_hewan'), $kv('ukuran'), $emptyDash($kondisi), $emptyDash($inv->tahun_perolehan), $kv('tahun_buku')],
            'F' => [$emptyDash($kondisi), $kv('tahun_buku'), $kv('dokumen_tgl'), $kv('dokumen_nomor'), $kv('bidang'), $kv('sumber_dana')],
            default => [],
        };

        $tail = [$emptyDash($asalUsul), $emptyDash($harga)];
        if ($this->kibType === 'D') $tail[] = $emptyDash($kondisi);
        $tail[] = $emptyDash($inv->keterangan);

        return array_merge($base, $extra, $tail);
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 20,  // NO (expanded so Info Desa labels fit fully)
            'B' => 28,  // Nama Barang
            'C' => 18,  // Kode Barang
            'D' => 8,   // REG
        ];
        
        // Determine the last column letter for the current KIB type
        $lastColLetter = match($this->kibType) {
            'A' => 'N',
            'B' => 'Q',
            'C' => 'Q',
            'D' => 'S',
            'E' => 'Q',
            'F' => 'M',
            default => 'H'
        };
        
        // Set standard width of 16 for all columns from E to the last column
        $lastColNum = $this->colLetterToNum($lastColLetter);
        for ($i = 5; $i <= $lastColNum; $i++) {
            $letter = $this->numToColLetter($i);
            $widths[$letter] = 16;
        }
        
        switch ($this->kibType) {
            case 'A':
                $widths['E'] = 14; // Luas
                $widths['H'] = 30; // Alamat
                $widths['N'] = 25; // Keterangan
                break;
            case 'B':
                $widths['E'] = 20; // Merk/Type
                $widths['Q'] = 25; // Keterangan
                break;
            case 'C':
                $widths['M'] = 30; // Alamat
                break;
            case 'D':
                $widths['M'] = 30; // Alamat
                $widths['S'] = 25; // Keterangan
                break;
            case 'E':
                $widths['E'] = 24; // Judul
                $widths['Q'] = 25; // Keterangan
                break;
            case 'F':
                $widths['M'] = 25; // Keterangan
                break;
        }
        
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $sheet->getHighestColumn();
                $totalCols = $this->colLetterToNum($lastCol);

                // Insert 13 empty rows at the top for title, period, and info desa
                $sheet->insertNewRowBefore(1, 13);

                // Title (Row 1)
                $sheet->setCellValue('A1', $this->kibConfig['judul_export'] ?? '');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Period (Row 2)
                $tahun = $this->filters['tahun'] ?? date('Y');
                $sheet->setCellValue('A2', 'Periode Tanggal, 31 Desember ' . $tahun);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(16);
                $sheet->getRowDimension(3)->setRowHeight(10); // spacer row

                // Info Desa Block (Rows 4 - 9)
                $desa = config('desa', []);
                $infoKiri = [
                    ['Provinsi',        ($desa['provinsi_kode'] ?? '') . '. ' . ($desa['provinsi_nama'] ?? '')],
                    ['Kab./Kota',       ($desa['kabupaten_kode'] ?? '') . '. ' . ($desa['kabupaten_nama'] ?? '')],
                    ['Kecamatan',       ($desa['kecamatan_kode'] ?? '') . '. ' . ($desa['kecamatan_nama'] ?? '')],
                    ['Desa',            ($desa['kode_lokasi'] ?? '') . '. ' . ($desa['desa_nama'] ?? '')],
                    ['UPB',             $desa['upb'] ?? ''],
                    ['No. Kode Lokasi', ($desa['kode_lokasi'] ?? '') . '.'],
                ];
                $rr = 4;
                $pad = 15;
                foreach ($infoKiri as [$lbl, $val]) {
                    $sheet->setCellValue("A{$rr}", str_pad($lbl, $pad) . ' :');
                    $sheet->setCellValue("B{$rr}", $val);
                    $sheet->getStyle("A{$rr}")->getFont()->setName('Courier New')->setSize(9);
                    $sheet->getStyle("B{$rr}")->getFont()->setBold(true)->setSize(9.5);
                    $sheet->getStyle("A{$rr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B{$rr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $rr++;
                }

                $headRow = 11;
                $subRow  = 12;
                $numRow  = 13;
                $dataStart = 14;

                // Set headers and merge cells
                switch ($this->kibType) {
                    case 'A':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "IDENTITAS"); $sheet->mergeCells("E{$headRow}:H{$headRow}");
                        $sheet->setCellValue("E{$subRow}", "LUAS (M2)");
                        $sheet->setCellValue("F{$subRow}", "TAHUN PEROLEHAN");
                        $sheet->setCellValue("G{$subRow}", "BUKU");
                        $sheet->setCellValue("H{$subRow}", "ALAMAT");
                        $sheet->setCellValue("I{$headRow}", "STATUS TANAH"); $sheet->mergeCells("I{$headRow}:K{$headRow}");
                        $sheet->setCellValue("I{$subRow}", "HAK");
                        $sheet->setCellValue("J{$subRow}", "NOMOR");
                        $sheet->setCellValue("K{$subRow}", "TGL");
                        $sheet->setCellValue("L{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("L{$headRow}:L{$subRow}");
                        $sheet->setCellValue("M{$headRow}", "HARGA\nRp"); $sheet->mergeCells("M{$headRow}:M{$subRow}");
                        $sheet->setCellValue("N{$headRow}", "KETERANGAN"); $sheet->mergeCells("N{$headRow}:N{$subRow}");
                        break;
                    case 'B':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "IDENTITAS"); $sheet->mergeCells("E{$headRow}:I{$headRow}");
                        $sheet->setCellValue("E{$subRow}", "MERK/TYPE");
                        $sheet->setCellValue("F{$subRow}", "UKURAN/AN/CC");
                        $sheet->setCellValue("G{$subRow}", "BAHAN");
                        $sheet->setCellValue("H{$subRow}", "TAHUN PEROLEHAN");
                        $sheet->setCellValue("I{$subRow}", "BUKU");
                        $sheet->setCellValue("J{$headRow}", "NOMOR"); $sheet->mergeCells("J{$headRow}:N{$headRow}");
                        $sheet->setCellValue("J{$subRow}", "PABRIK");
                        $sheet->setCellValue("K{$subRow}", "RANGKA");
                        $sheet->setCellValue("L{$subRow}", "MESIN");
                        $sheet->setCellValue("M{$subRow}", "POLISI");
                        $sheet->setCellValue("N{$subRow}", "BPKB");
                        $sheet->setCellValue("O{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("O{$headRow}:O{$subRow}");
                        $sheet->setCellValue("P{$headRow}", "HARGA\nRp"); $sheet->mergeCells("P{$headRow}:P{$subRow}");
                        $sheet->setCellValue("Q{$headRow}", "KETERANGAN"); $sheet->mergeCells("Q{$headRow}:Q{$subRow}");
                        break;
                    case 'C':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "KONDISI\nBANGUNAN\n(B,RR,RB)"); $sheet->mergeCells("E{$headRow}:E{$subRow}");
                        $sheet->setCellValue("F{$headRow}", "KONSTRUKSI BANGUNAN"); $sheet->mergeCells("F{$headRow}:G{$headRow}");
                        $sheet->setCellValue("F{$subRow}", "BERTINGKAT AT/TIDAK");
                        $sheet->setCellValue("G{$subRow}", "BETON/TIDAK");
                        $sheet->setCellValue("H{$headRow}", "IDENTITAS"); $sheet->mergeCells("H{$headRow}:M{$headRow}");
                        $sheet->setCellValue("H{$subRow}", "LUAS (M2)");
                        $sheet->setCellValue("I{$subRow}", "TAHUN PEROLEHAN");
                        $sheet->setCellValue("J{$subRow}", "BUKU");
                        $sheet->setCellValue("K{$subRow}", "TANGGAL");
                        $sheet->setCellValue("L{$subRow}", "NOMOR");
                        $sheet->setCellValue("M{$subRow}", "ALAMAT");
                        $sheet->setCellValue("N{$headRow}", "STATUS\nTANAH"); $sheet->mergeCells("N{$headRow}:N{$subRow}");
                        $sheet->setCellValue("O{$headRow}", "NOMOR KODE\nTANAH"); $sheet->mergeCells("O{$headRow}:O{$subRow}");
                        $sheet->setCellValue("P{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("P{$headRow}:P{$subRow}");
                        $sheet->setCellValue("Q{$headRow}", "HARGA\nRp"); $sheet->mergeCells("Q{$headRow}:Q{$subRow}");
                        break;
                    case 'D':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "KONS-\nTRUKSI"); $sheet->mergeCells("E{$headRow}:E{$subRow}");
                        $sheet->setCellValue("F{$headRow}", "IDENTITAS"); $sheet->mergeCells("F{$headRow}:M{$headRow}");
                        $sheet->setCellValue("F{$subRow}", "PANJANG (KM)");
                        $sheet->setCellValue("G{$subRow}", "LEBAR (M)");
                        $sheet->setCellValue("H{$subRow}", "LUAS (M2)");
                        $sheet->setCellValue("I{$subRow}", "TAHUN PEROLEHAN");
                        $sheet->setCellValue("J{$subRow}", "BUKU");
                        $sheet->setCellValue("K{$subRow}", "TGL");
                        $sheet->setCellValue("L{$subRow}", "NOMOR");
                        $sheet->setCellValue("M{$subRow}", "ALAMAT");
                        $sheet->setCellValue("N{$headRow}", "TANAH"); $sheet->mergeCells("N{$headRow}:O{$headRow}");
                        $sheet->setCellValue("N{$subRow}", "STATUS");
                        $sheet->setCellValue("O{$subRow}", "KODE");
                        $sheet->setCellValue("P{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("P{$headRow}:P{$subRow}");
                        $sheet->setCellValue("Q{$headRow}", "HARGA\nRp"); $sheet->mergeCells("Q{$headRow}:Q{$subRow}");
                        $sheet->setCellValue("R{$headRow}", "KONDISI"); $sheet->mergeCells("R{$headRow}:R{$subRow}");
                        $sheet->setCellValue("S{$headRow}", "KETERANGAN"); $sheet->mergeCells("S{$headRow}:S{$subRow}");
                        break;
                    case 'E':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "BUKU /\nPERPUSTAKAAN"); $sheet->mergeCells("E{$headRow}:F{$headRow}");
                        $sheet->setCellValue("E{$subRow}", "JUDUL/PENCIPTA");
                        $sheet->setCellValue("F{$subRow}", "SPESIFI-KASI");
                        $sheet->setCellValue("G{$headRow}", "BARANG BERCORAK\nKESENIAN/KEBUDAYAAN"); $sheet->mergeCells("G{$headRow}:I{$headRow}");
                        $sheet->setCellValue("G{$subRow}", "ASAL DAERAH");
                        $sheet->setCellValue("H{$subRow}", "PENCIPTA");
                        $sheet->setCellValue("I{$subRow}", "BAHAN");
                        $sheet->setCellValue("J{$headRow}", "HEWAN/TERNAK\nDAN TUMBUHAN"); $sheet->mergeCells("J{$headRow}:K{$headRow}");
                        $sheet->setCellValue("J{$subRow}", "JENIS");
                        $sheet->setCellValue("K{$subRow}", "UKURAN");
                        $sheet->setCellValue("L{$headRow}", "KONDISI"); $sheet->mergeCells("L{$headRow}:L{$subRow}");
                        $sheet->setCellValue("M{$headRow}", "TAHUN\nPEROLEHAN/BUKU"); $sheet->mergeCells("M{$headRow}:N{$headRow}");
                        $sheet->setCellValue("M{$subRow}", "PEROLEHAN");
                        $sheet->setCellValue("N{$subRow}", "BUKU");
                        $sheet->setCellValue("O{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("O{$headRow}:O{$subRow}");
                        $sheet->setCellValue("P{$headRow}", "HARGA\nRp"); $sheet->mergeCells("P{$headRow}:P{$subRow}");
                        $sheet->setCellValue("Q{$headRow}", "KETERANGAN"); $sheet->mergeCells("Q{$headRow}:Q{$subRow}");
                        break;
                    case 'F':
                        $sheet->setCellValue("A{$headRow}", "NO"); $sheet->mergeCells("A{$headRow}:A{$subRow}");
                        $sheet->setCellValue("B{$headRow}", "JENIS BARANG / NAMA BARANG"); $sheet->mergeCells("B{$headRow}:B{$subRow}");
                        $sheet->setCellValue("C{$headRow}", "NOMOR"); $sheet->mergeCells("C{$headRow}:D{$headRow}");
                        $sheet->setCellValue("C{$subRow}", "KODE BARANG");
                        $sheet->setCellValue("D{$subRow}", "REG.");
                        $sheet->setCellValue("E{$headRow}", "KONDISI\nBANGUNAN\n(B,RR,RB)"); $sheet->mergeCells("E{$headRow}:E{$subRow}");
                        $sheet->setCellValue("F{$headRow}", "IDENTITAS"); $sheet->mergeCells("F{$headRow}:H{$headRow}");
                        $sheet->setCellValue("F{$subRow}", "TAHUN BUKU");
                        $sheet->setCellValue("G{$subRow}", "TGL DOKUMEN");
                        $sheet->setCellValue("H{$subRow}", "NO DOKUMEN");
                        $sheet->setCellValue("I{$headRow}", "BIDANG"); $sheet->mergeCells("I{$headRow}:I{$subRow}");
                        $sheet->setCellValue("J{$headRow}", "SUMBER\nDANA"); $sheet->mergeCells("J{$headRow}:J{$subRow}");
                        $sheet->setCellValue("K{$headRow}", "ASAL-USUL BARANG"); $sheet->mergeCells("K{$headRow}:K{$subRow}");
                        $sheet->setCellValue("L{$headRow}", "HARGA\nRp"); $sheet->mergeCells("L{$headRow}:L{$subRow}");
                        $sheet->setCellValue("M{$headRow}", "KETERANGAN"); $sheet->mergeCells("M{$headRow}:M{$subRow}");
                        break;
                }

                // Write column numbers (Row 13) with proper merges matching top-level columns
                switch ($this->kibType) {
                    case 'A':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4); $sheet->mergeCells("E{$numRow}:H{$numRow}");
                        $sheet->setCellValue("I{$numRow}", 5); $sheet->mergeCells("I{$numRow}:K{$numRow}");
                        $sheet->setCellValue("L{$numRow}", 6);
                        $sheet->setCellValue("M{$numRow}", 7);
                        $sheet->setCellValue("N{$numRow}", 8);
                        break;
                    case 'B':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4); $sheet->mergeCells("E{$numRow}:I{$numRow}");
                        $sheet->setCellValue("J{$numRow}", 5); $sheet->mergeCells("J{$numRow}:N{$numRow}");
                        $sheet->setCellValue("O{$numRow}", 6);
                        $sheet->setCellValue("P{$numRow}", 7);
                        $sheet->setCellValue("Q{$numRow}", 8);
                        break;
                    case 'C':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4);
                        $sheet->setCellValue("F{$numRow}", 5); $sheet->mergeCells("F{$numRow}:G{$numRow}");
                        $sheet->setCellValue("H{$numRow}", 6); $sheet->mergeCells("H{$numRow}:M{$numRow}");
                        $sheet->setCellValue("N{$numRow}", 7);
                        $sheet->setCellValue("O{$numRow}", 8);
                        $sheet->setCellValue("P{$numRow}", 9);
                        $sheet->setCellValue("Q{$numRow}", 10);
                        break;
                    case 'D':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4);
                        $sheet->setCellValue("F{$numRow}", 5); $sheet->mergeCells("F{$numRow}:M{$numRow}");
                        $sheet->setCellValue("N{$numRow}", 6); $sheet->mergeCells("N{$numRow}:O{$numRow}");
                        $sheet->setCellValue("P{$numRow}", 7);
                        $sheet->setCellValue("Q{$numRow}", 8);
                        $sheet->setCellValue("R{$numRow}", 9);
                        $sheet->setCellValue("S{$numRow}", 10);
                        break;
                    case 'E':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4); $sheet->mergeCells("E{$numRow}:F{$numRow}");
                        $sheet->setCellValue("G{$numRow}", 5); $sheet->mergeCells("G{$numRow}:I{$numRow}");
                        $sheet->setCellValue("J{$numRow}", 6); $sheet->mergeCells("J{$numRow}:K{$numRow}");
                        $sheet->setCellValue("L{$numRow}", 7);
                        $sheet->setCellValue("O{$numRow}", 8);
                        $sheet->setCellValue("M{$numRow}", 9); $sheet->mergeCells("M{$numRow}:N{$numRow}");
                        $sheet->setCellValue("P{$numRow}", 10);
                        $sheet->setCellValue("Q{$numRow}", 11);
                        break;
                    case 'F':
                        $sheet->setCellValue("A{$numRow}", 1);
                        $sheet->setCellValue("B{$numRow}", 2);
                        $sheet->setCellValue("C{$numRow}", 3); $sheet->mergeCells("C{$numRow}:D{$numRow}");
                        $sheet->setCellValue("E{$numRow}", 4);
                        $sheet->setCellValue("F{$numRow}", 5); $sheet->mergeCells("F{$numRow}:H{$numRow}");
                        $sheet->setCellValue("I{$numRow}", 6);
                        $sheet->setCellValue("J{$numRow}", 7);
                        $sheet->setCellValue("K{$numRow}", 8);
                        $sheet->setCellValue("L{$numRow}", 9);
                        $sheet->setCellValue("M{$numRow}", 10);
                        break;
                }

                // Style header and column numbers block
                $sheet->getStyle("A{$headRow}:{$lastCol}{$numRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension($headRow)->setRowHeight(25);
                $sheet->getRowDimension($subRow)->setRowHeight(25);
                $sheet->getRowDimension($numRow)->setRowHeight(16);

                // Style data rows
                $actualLastRow = $sheet->getHighestRow();
                if ($actualLastRow >= $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$actualLastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font' => ['size' => 9],
                        'alignment' => ['wrapText' => true],
                    ]);
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$actualLastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    for ($r = $dataStart; $r <= $actualLastRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    }

                    // Align columns
                    $sheet->getStyle("A{$dataStart}:A{$actualLastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$dataStart}:D{$actualLastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Ensure specific code / text columns don't show warnings in Excel
                    $sheet->getStyle("C{$dataStart}:D{$actualLastRow}")
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                    switch ($this->kibType) {
                        case 'A':
                            $this->alignCenter($sheet, ["E", "F", "G", "I", "K"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["M"], $dataStart, $actualLastRow);
                            break;
                        case 'B':
                            $this->alignCenter($sheet, ["F", "H", "I"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["P"], $dataStart, $actualLastRow);
                            break;
                        case 'C':
                            $this->alignCenter($sheet, ["E", "H", "I", "J", "K"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["Q"], $dataStart, $actualLastRow);
                            break;
                        case 'D':
                            $this->alignCenter($sheet, ["F", "G", "H", "I", "J", "K", "R"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["Q"], $dataStart, $actualLastRow);
                            break;
                        case 'E':
                            $this->alignCenter($sheet, ["L", "M", "N"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["P"], $dataStart, $actualLastRow);
                            break;
                        case 'F':
                            $this->alignCenter($sheet, ["E", "F", "G"], $dataStart, $actualLastRow);
                            $this->alignRight($sheet, ["L"], $dataStart, $actualLastRow);
                            break;
                    }
                }

                // Jumlah row
                $jumlahRow = ($actualLastRow >= $dataStart) ? $actualLastRow + 1 : $dataStart;
                $hargaColLetter = $this->numToColLetter($this->getHargaColIndex() + 1);
                $mergeEndLetter = $this->numToColLetter($this->getHargaColIndex());

                $sheet->mergeCells("A{$jumlahRow}:{$mergeEndLetter}{$jumlahRow}");
                $sheet->setCellValue("A{$jumlahRow}", 'JUMLAH HARGA');
                $sheet->setCellValue("{$hargaColLetter}{$jumlahRow}", number_format($this->totalHarga, 2, ',', '.'));

                $sheet->getStyle("A{$jumlahRow}:{$lastCol}{$jumlahRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$hargaColLetter}{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("{$hargaColLetter}{$jumlahRow}")
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->getRowDimension($jumlahRow)->setRowHeight(22);

                // Signature block (Tim Inventarisasi / Pejabat)
                $kiriNama  = trim((string)($this->pejabatKiri['nama'] ?? ''));
                $kananNama = trim((string)($this->pejabatKanan['nama'] ?? ''));

                if ($kiriNama !== '' || $kananNama !== '') {
                    $ttdRow = $jumlahRow + 2;
                    $kiriStart = "C";
                    $kiriEnd = "H";
                    
                    $kananStartNum = max(9, $totalCols - 5);
                    $kananStart = $this->numToColLetter($kananStartNum);
                    $kananEnd = $lastCol;
                    
                    // Kiri: Sekretaris Desa
                    $sheet->setCellValue("{$kiriStart}{$ttdRow}", "MENGETAHUI");
                    $sheet->mergeCells("{$kiriStart}{$ttdRow}:{$kiriEnd}{$ttdRow}");
                    $sheet->setCellValue("{$kiriStart}" . ($ttdRow + 1), "SEKRETARIS DESA");
                    $sheet->mergeCells("{$kiriStart}" . ($ttdRow + 1) . ":{$kiriEnd}" . ($ttdRow + 1));
                    $sheet->setCellValue("{$kiriStart}" . ($ttdRow + 2), "Selaku Pembantu Pengelola Barang Milik Desa");
                    $sheet->mergeCells("{$kiriStart}" . ($ttdRow + 2) . ":{$kiriEnd}" . ($ttdRow + 2));
                    
                    // Kanan: Petugas Barang
                    $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                              7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    $tglStr = '';
                    if ($this->tanggalDokumen) {
                        try {
                            $dt = \Carbon\Carbon::parse($this->tanggalDokumen);
                            $tglStr = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
                        } catch (\Throwable $e) {}
                    }
                    if (!$tglStr) {
                        $now = now();
                        $tglStr = $now->day . ' ' . $bulan[(int)$now->format('n')] . ' ' . $now->year;
                    }
                    $loc = $this->keteranganDokumen ?: (config('desa.nama_lokasi') ?? '');
                    $tanggalDisplay = ($loc ? $loc . ', ' : '') . $tglStr;
                    
                    $sheet->setCellValue("{$kananStart}{$ttdRow}", $tanggalDisplay);
                    $sheet->mergeCells("{$kananStart}{$ttdRow}:{$kananEnd}{$ttdRow}");
                    $sheet->setCellValue("{$kananStart}" . ($ttdRow + 1), "PETUGAS / PENGURUS BARANG MILIK DESA");
                    $sheet->mergeCells("{$kananStart}" . ($ttdRow + 1) . ":{$kananEnd}" . ($ttdRow + 1));
                    
                    $sheet->getStyle("{$kiriStart}{$ttdRow}:{$kananEnd}" . ($ttdRow + 2))->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9.5],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);
                    $sheet->getStyle("{$kiriStart}" . ($ttdRow + 2))->applyFromArray([
                        'font' => ['bold' => false, 'size' => 8.5],
                    ]);
                    
                    $sheet->getRowDimension($ttdRow + 3)->setRowHeight(20);
                    $sheet->getRowDimension($ttdRow + 4)->setRowHeight(20);
                    
                    $namaRow = $ttdRow + 5;
                    $sheet->setCellValue("{$kiriStart}{$namaRow}", $this->pejabatKiri['nama'] ?? '');
                    $sheet->mergeCells("{$kiriStart}{$namaRow}:{$kiriEnd}{$namaRow}");
                    $sheet->setCellValue("{$kananStart}{$namaRow}", $this->pejabatKanan['nama'] ?? '');
                    $sheet->mergeCells("{$kananStart}{$namaRow}:{$kananEnd}{$namaRow}");
                    
                    $sheet->getStyle("{$kiriStart}{$namaRow}:{$kananEnd}{$namaRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'underline' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                // Set column widths explicitly to ensure they are applied correctly at the very end
                foreach ($this->columnWidths() as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Print settings
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
            },
        ];
    }

    protected function getHargaColIndex(): int
    {
        return match($this->kibType) {
            'A' => 12, 'B' => 15, 'C' => 16, 'D' => 16, 'E' => 15, 'F' => 11,
            default => 8,
        };
    }

    protected function alignCenter($sheet, array $columns, int $start, int $end): void
    {
        foreach ($columns as $col) {
            $sheet->getStyle("{$col}{$start}:{$col}{$end}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    protected function alignRight($sheet, array $columns, int $start, int $end): void
    {
        foreach ($columns as $col) {
            $sheet->getStyle("{$col}{$start}:{$col}{$end}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("{$col}{$start}:{$col}{$end}")
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        }
    }

    protected function numToColLetter(int $colNum): string
    {
        $letter = '';
        while ($colNum > 0) {
            $temp = ($colNum - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $colNum = ($colNum - $temp - 1) / 26;
        }
        return $letter;
    }

    protected function colLetterToNum(string $colLetter): int
    {
        $num = 0;
        $len = strlen($colLetter);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($colLetter[$i]) - 64);
        }
        return $num;
    }
}
