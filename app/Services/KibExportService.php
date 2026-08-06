<?php

namespace App\Services;

use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\KibPejabatDefault;
use App\Models\Pejabat;
use App\Models\ExportLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class KibExportService
{
    protected string $disk = 'local';

    /**
     * Export satu KIB atau semua KIB sekaligus.
     */
    public function export(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $kibType           = $opts['kib_type'] ?? 'all';
        $format            = strtolower($opts['format'] ?? 'pdf');
        $filters           = $opts['filters'] ?? [];
        $pejabatOverride   = $opts['pejabat_override'] ?? [];
        $signatureOption   = $opts['signature_option'] ?? 'with';
        $tanggalDokumen    = $opts['tanggal_dokumen'] ?? '';
        $keteranganDokumen = $opts['keterangan_dokumen'] ?? '';
        $paperSize         = $opts['paper_size'] ?? 'legal';
        $userId            = $opts['user_id'] ?? null;
        $includeImg        = ($signatureOption !== 'without');

        $pejabatKiri  = $this->resolvePejabat($kibType, 'kiri', $pejabatOverride);
        $pejabatKanan = $this->resolvePejabat($kibType, 'kanan', $pejabatOverride);

        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp = Carbon::now()->format('Ymd_His');

        if ($kibType === 'all') {
            return $this->exportAll(
                $format, $filters, $pejabatKiri, $pejabatKanan,
                $includeImg, $tanggalDokumen, $keteranganDokumen,
                $paperSize, $baseDir, $timestamp, $userId
            );
        }

        return $this->exportSingle(
            $kibType, $format, $filters, $pejabatKiri, $pejabatKanan,
            $includeImg, $tanggalDokumen, $keteranganDokumen,
            $paperSize, $baseDir, $timestamp, $userId
        );
    }

    protected function resolvePejabat(string $kibType, string $posisi, array $override): ?array
    {
        // Override dari UI
        if (!empty($override[$posisi])) {
            $pe = Pejabat::find($override[$posisi]);
            return $pe ? $pe->toArray() : null;
        }

        // Default dari kib_pejabat_defaults
        $default = KibPejabatDefault::where('kib_type', $kibType === 'all' ? 'A' : $kibType)
            ->where('posisi', $posisi)
            ->with('pejabat')
            ->first();
        if ($default?->pejabat) {
            return $default->pejabat->toArray();
        }

        // Fallback: Sekretaris Desa (kiri) / Petugas Barang (kanan)
        if ($posisi === 'kiri') {
            $user = \App\Models\User::with(['role', 'pejabat'])
                ->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))
                ->first();
            return $user?->pejabat?->toArray();
        }

        $pe = Pejabat::where('is_petugas_barang', 1)->first();
        return $pe?->toArray();
    }

    protected function exportSingle(
        string $kibType, string $format, array $filters,
        ?array $pejabatKiri, ?array $pejabatKanan,
        bool $includeImg, string $tanggalDokumen, string $keteranganDokumen,
        string $paperSize, string $baseDir, string $timestamp, ?int $userId
    ): array {
        $kibConfig = config('kib.' . $kibType);
        if (!$kibConfig) {
            throw new \InvalidArgumentException('KIB type tidak valid: ' . $kibType);
        }

        $inventarisList = $this->getInventarisForKib($kibType, $filters);
        $filename = 'kib_' . strtolower($kibType) . '_' . $timestamp . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
        $relativePath = $baseDir . '/' . $filename;

        if ($format === 'pdf') {
            $html = $this->renderKibPdf(
                $kibType, $kibConfig, $inventarisList,
                $pejabatKiri, $pejabatKanan, $includeImg,
                $tanggalDokumen, $keteranganDokumen, $paperSize, $filters
            );
            $this->savePdf($html, $relativePath, $paperSize);
        } elseif ($format === 'excel') {
            $this->saveExcel($kibType, $kibConfig, $inventarisList, $pejabatKiri, $pejabatKanan, $includeImg, $tanggalDokumen, $keteranganDokumen, $filters, $relativePath);
        } else {
            throw new \InvalidArgumentException('Format tidak valid: ' . $format);
        }

        $log = ExportLog::create([
            'user_id'             => $userId,
            'jenis_inventaris_id' => null,
            'tipe_export'         => $format,
            'nama_file'           => $filename,
            'pejabat_override'    => array_filter([$pejabatKiri, $pejabatKanan]),
            'filters'             => array_merge($filters, ['kib_type' => $kibType, 'mode' => 'kib']),
        ]);

        return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
    }

    protected function exportAll(
        string $format, array $filters,
        ?array $pejabatKiri, ?array $pejabatKanan,
        bool $includeImg, string $tanggalDokumen, string $keteranganDokumen,
        string $paperSize, string $baseDir, string $timestamp, ?int $userId
    ): array {
        $kibTypes = ['A', 'B', 'C', 'D', 'E', 'F'];
        $filename = 'kib_semua_' . $timestamp . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
        $relativePath = $baseDir . '/' . $filename;

        if ($format === 'pdf') {
            $segments = [];
            foreach ($kibTypes as $kibType) {
                $kibConfig = config('kib.' . $kibType);
                if (!$kibConfig) continue;
                $inventarisList = $this->getInventarisForKib($kibType, $filters);
                // Skip KIB type yang tidak punya data — tidak render halaman kosong
                if ($inventarisList->isEmpty()) continue;
                $pejabatK = $this->resolvePejabat($kibType, 'kiri', []);
                $pejabatKn = $this->resolvePejabat($kibType, 'kanan', []);
                $segments[] = $this->renderKibPdf(
                    $kibType, $kibConfig, $inventarisList,
                    $pejabatK, $pejabatKn, $includeImg,
                    $tanggalDokumen, $keteranganDokumen, $paperSize, $filters, true
                );
            }
            $allHtml = implode('<div class="page-break"></div>', $segments);
            $css = $this->getKibPdfCss($paperSize);
            
            $footerScript = '
                <span style="font-weight: normal; text-decoration: none; font-style: normal; display: none;"></span>
                <script type="text/php">
                    if (isset($pdf)) {
                        $pdf->page_script(\'
                            $w = $pdf->get_width();
                            $h = $pdf->get_height();
                            $font = $fontMetrics->getFont("helvetica", "normal");
                            $size = 9.5;
                            $col = [0, 0, 0];
                            $yText = $h - 36;
                            $yLine = $h - 20;

                            // Render garis pembatas underscore
                            $underscoreWidth = $fontMetrics->getTextWidth("_", $font, 9);
                            $numChars = (int)(($w - 40) / $underscoreWidth);
                            $lineText = str_repeat("_", $numChars);
                            $pdf->text(20, $yLine, $lineText, $font, 9, $col);

                            // Cari label KIB yang sesuai untuk halaman ini
                            $currentLabel = "Laporan Buku Inventaris Aset Desa";
                            if (isset($GLOBALS["kib_pages"]) && is_array($GLOBALS["kib_pages"])) {
                                $pages = $GLOBALS["kib_pages"];
                                ksort($pages);
                                foreach ($pages as $pNum => $label) {
                                    if ($PAGE_NUM >= $pNum) {
                                        $currentLabel = $label;
                                    } else {
                                        break;
                                    }
                                }
                            }
                            
                            $pdf->text(20, $yText, $currentLabel, $font, $size, $col);
                            
                            $rightText = "Halaman : " . $PAGE_NUM . " dari " . $PAGE_COUNT;
                            $tw = $fontMetrics->getTextWidth("Halaman : 1 dari 1", $font, $size);
                            $pdf->text($w - $tw - 20, $yText, $rightText, $font, $size, $col);
                        \');
                    }
                </script>
            ';

            $fullHtml = '<!doctype html><html><head><meta charset="utf-8">'
                . '<style>' . $css . '</style></head><body>'
                . $allHtml . $footerScript . '</body></html>';
            $this->savePdf($fullHtml, $relativePath, $paperSize, true);
        } else {
            // Excel: multi-sheet, satu sheet per KIB — skip yang tidak punya data
            $sheets = [];
            foreach ($kibTypes as $kibType) {
                $kibConfig = config('kib.' . $kibType);
                if (!$kibConfig) continue;
                $inventarisList = $this->getInventarisForKib($kibType, $filters);
                // Skip KIB type yang tidak punya data — tidak buat sheet kosong
                if ($inventarisList->isEmpty()) continue;
                $pejabatK = $this->resolvePejabat($kibType, 'kiri', []);
                $pejabatKn = $this->resolvePejabat($kibType, 'kanan', []);
                $sheets[] = new \App\Exports\Kib\KibSheetExport(
                    $kibType, $kibConfig, $inventarisList,
                    $pejabatK, $pejabatKn, $includeImg,
                    $tanggalDokumen, $keteranganDokumen, $filters
                );
            }
            \Maatwebsite\Excel\Facades\Excel::store(
                new \App\Exports\Kib\KibAllExport($sheets),
                $relativePath, $this->disk
            );
        }

        $log = ExportLog::create([
            'user_id'          => $userId,
            'tipe_export'      => $format,
            'nama_file'        => $filename,
            'pejabat_override' => [],
            'filters'          => array_merge($filters, ['kib_type' => 'all', 'mode' => 'kib']),
        ]);

        return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
    }

    protected function getInventarisForKib(string $kibType, array $filters): \Illuminate\Support\Collection
    {
        $query = Inventaris::with(['jenis', 'kibValues'])
            ->whereHas('jenis', fn($q) => $q->where('kib_type', $kibType))
            ->orderBy('kode_barang');

        if (!empty($filters['tahun'])) {
            $query->whereYear('created_at', $filters['tahun']);
        }

        return $query->get();
    }

    protected function renderKibPdf(
        string $kibType, array $kibConfig,
        \Illuminate\Support\Collection $inventarisList,
        ?array $pejabatKiri, ?array $pejabatKanan,
        bool $includeImg, string $tanggalDokumen, string $keteranganDokumen,
        string $paperSize, array $filters, bool $bodyOnly = false
    ): string {
        $desa = config('desa');
        $pageSizeCss = $this->pageSizeCss($paperSize);
        $tahun = $filters['tahun'] ?? date('Y');

        $tanggalFormatted = '';
        if ($tanggalDokumen) {
            try {
                $dt = \Carbon\Carbon::parse($tanggalDokumen);
                $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                          7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                $loc = $keteranganDokumen ?: ($desa['nama_lokasi'] ?? '');
                $tanggalFormatted = ($loc ? $loc . ', ' : '') . $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {}
        }

        $css = $this->getKibPdfCss($paperSize);

        $namaKib = $kibConfig['nama'] ?? ('KIB ' . $kibType);
        $prefix = str_starts_with(strtolower($namaKib), 'aset') ? '' : 'Aset ';
        $footerLabel = 'Laporan Buku Inventaris Aset Desa ' . $prefix . $namaKib . ' (KIB ' . $kibType . ')';

        $html = $bodyOnly ? '' : '<!doctype html><html><head><meta charset="utf-8"><style>' . $css . '</style></head><body>';

        if ($bodyOnly) {
            $html .= '
                <script type="text/php">
                    $GLOBALS["kib_pages"][$pdf->get_page_number()] = "' . addslashes($footerLabel) . '";
                </script>
            ';
        }

        // Header
        $html .= '<div class="doc-title">' . htmlentities($kibConfig['judul_export']) . '</div>';
        $html .= '<div class="doc-subtitle">Periode Tanggal, ' . ($tanggalFormatted ?: date('d') . ' ' . date('F') . ' ' . $tahun) . '</div>';

        // Info desa
        $html .= '<table class="info-table">';
        $html .= '<tr><td>Provinsi</td><td>:</td><td>' . htmlentities(($desa['provinsi_kode'] ?? '') . ($desa['provinsi_kode'] && $desa['provinsi_nama'] ? '.  ' : '') . ($desa['provinsi_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Kab./Kota</td><td>:</td><td>' . htmlentities(($desa['kabupaten_kode'] ?? '') . ($desa['kabupaten_kode'] && $desa['kabupaten_nama'] ? '.  ' : '') . ($desa['kabupaten_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Kecamatan</td><td>:</td><td>' . htmlentities(($desa['kecamatan_kode'] ?? '') . ($desa['kecamatan_kode'] && $desa['kecamatan_nama'] ? '.  ' : '') . ($desa['kecamatan_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Desa</td><td>:</td><td>' . htmlentities($desa['desa_nama'] ?? '') . '</td></tr>';
        $html .= '<tr><td>UPB</td><td>:</td><td>' . htmlentities($desa['upb'] ?? '') . '</td></tr>';
        $html .= '<tr><td>No. Kode Lokasi</td><td>:</td><td>' . htmlentities($desa['kode_lokasi'] ?? '') . '</td></tr>';
        $html .= '</table>';

        // Tabel data
        $html .= $this->renderKibTable($kibType, $kibConfig, $inventarisList);

        // TTD
        $html .= $this->renderTtd($pejabatKiri, $pejabatKanan, $includeImg, $tanggalFormatted);

        if (!$bodyOnly) {
            $html .= \App\Services\ExportService::buildFooterHtml($footerLabel);
            $html .= '</body></html>';
        }

        return $html;
    }

    protected function renderKibTable(string $kibType, array $kibConfig, \Illuminate\Support\Collection $inventarisList): string
    {
        $fields = $kibConfig['fields'] ?? [];
        $html = '<table class="inv-table"><thead>';

        // Header baris 1
        $html .= '<tr>';
        $html .= '<th rowspan="2" class="no-col">NO</th>';
        $html .= '<th rowspan="2">JENIS BARANG /<br>NAMA BARANG</th>';
        $html .= '<th colspan="2">NOMOR</th>';

        // Kolom khusus per KIB
        $html .= $this->renderKibTableHeader($kibType, $fields);

        $html .= '<th rowspan="2">ASAL-USUL<br>BARANG</th>';
        $html .= '<th rowspan="2">HARGA<br>Rp</th>';

        // Kondisi di akhir untuk KIB D
        if ($kibType === 'D') {
            $html .= '<th rowspan="2">KONDISI</th>';
        }
        if (in_array($kibType, ['A', 'B', 'D', 'E', 'F'])) {
            $html .= '<th rowspan="2">KETERANGAN</th>';
        }
        $html .= '</tr>';

        // Header baris 2 (sub-kolom)
        $html .= '<tr>';
        $html .= '<th>KODE BARANG</th><th>REG</th>';
        $html .= $this->renderKibTableSubHeader($kibType, $fields);
        $html .= '</tr>';

        // Header baris 3 (nomor kolom horizontal)
        $html .= '<tr>';
        switch ($kibType) {
            case 'A':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th colspan="4">4</th><th colspan="3">5</th><th>6</th><th>7</th><th>8</th>';
                break;
            case 'B':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th colspan="5">4</th><th colspan="5">5</th><th>6</th><th>7</th><th>8</th>';
                break;
            case 'C':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th>4</th><th colspan="2">5</th><th colspan="6">6</th><th>7</th><th>8</th><th>9</th><th>10</th>';
                break;
            case 'D':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th>4</th><th colspan="8">5</th><th colspan="2">6</th><th>7</th><th>8</th><th>9</th><th>10</th>';
                break;
            case 'E':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th colspan="2">4</th><th colspan="3">5</th><th colspan="2">6</th><th>7</th><th>8</th><th colspan="2">9</th><th>10</th><th>11</th>';
                break;
            case 'F':
                $html .= '<th>1</th><th>2</th><th colspan="2">3</th><th>4</th><th colspan="3">5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th>';
                break;
        }
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        // Data rows
        $no = 1;
        $totalHarga = 0.0;
        $emptyDash = fn($val) => ($val === null || trim((string)$val) === '') ? '-' : (string)$val;

        foreach ($inventarisList as $inv) {
            $nilai = (float)($inv->nilai_perolehan ?? 0);
            $totalHarga += $nilai;
            $asalUsulMap = ['apbdes' => 'APBDesa', 'perolehan_lain' => 'Perolehan Lain', 'kekayaan_asli' => 'Kekayaan Asli'];
            $asalUsul = $asalUsulMap[$inv->asal_usul ?? ''] ?? ($inv->asal_usul ?? '');
            $kondisiMap = ['baik' => 'B', 'rusak_ringan' => 'RR', 'rusak_berat' => 'RB'];
            $kondisi = $kondisiMap[$inv->kondisi ?? ''] ?? '';

            $displayName = $inv->display_name ?? $inv->nama_barang ?? '';
            $kodeBarang  = $inv->kode_barang ?? '';
            $nup         = $inv->nup ?? '';
            $keterangan  = $inv->keterangan ?? '';

            $html .= '<tr>';
            $html .= '<td class="no-col">' . $no++ . '</td>';
            $html .= '<td>' . htmlentities($emptyDash($displayName)) . '</td>';
            $html .= '<td>' . htmlentities($emptyDash($kodeBarang)) . '</td>';
            $html .= '<td>' . htmlentities($emptyDash($nup)) . '</td>';
            $html .= $this->renderKibTableRow($kibType, $fields, $inv, $emptyDash($kondisi));
            $html .= '<td>' . htmlentities($emptyDash($asalUsul)) . '</td>';
            $html .= '<td style="text-align:right;">' . ($nilai > 0 ? number_format($nilai, 2, ',', '.') : '-') . '</td>';
            if ($kibType === 'D') {
                $html .= '<td style="text-align:center;">' . htmlentities($emptyDash($kondisi)) . '</td>';
            }
            if (in_array($kibType, ['A', 'B', 'D', 'E', 'F'])) {
                $html .= '<td>' . htmlentities($emptyDash($keterangan)) . '</td>';
            }
            $html .= '</tr>';
        }

        // Jumlah row
        $colSpan = $this->getJumlahColSpan($kibType, $fields);
        $html .= '<tr class="jumlah-row"><td colspan="' . $colSpan . '">JUMLAH HARGA</td>';
        $html .= '<td style="text-align:right;">' . number_format($totalHarga, 2, ',', '.') . '</td>';
        if ($kibType === 'D') $html .= '<td></td>';
        if (in_array($kibType, ['A', 'B', 'D', 'E', 'F'])) $html .= '<td></td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';
        return $html;
    }

    protected function renderKibTableHeader(string $kibType, array $fields): string
    {
        switch ($kibType) {
            case 'A':
                return '<th colspan="4">IDENTITAS</th><th colspan="3">STATUS TANAH</th>';
            case 'B':
                return '<th colspan="5">IDENTITAS</th><th colspan="5">NOMOR</th>';
            case 'C':
                return '<th rowspan="2">KONDISI<br>BANGUNAN<br>(B,RR,RB)</th>'
                     . '<th colspan="2">KONSTRUKSI<br>BANGUNAN</th>'
                     . '<th colspan="6">IDENTITAS</th>'
                     . '<th rowspan="2">STATUS<br>TANAH</th>'
                     . '<th rowspan="2">NOMOR KODE<br>TANAH</th>';
            case 'D':
                return '<th rowspan="2">KONS-<br>TRUKSI</th>'
                     . '<th colspan="8">IDENTITAS</th>'
                     . '<th colspan="2">TANAH</th>';
            case 'E':
                return '<th colspan="2">BUKU/<br>PERPUSTAKAAN</th>'
                     . '<th colspan="3">BARANG BERCORAK<br>KESENIAN/KEBUDAYAAN</th>'
                     . '<th colspan="2">HEWAN/TERNAK<br>DAN TUMBUHAN</th>'
                     . '<th rowspan="2">KONDISI</th>'
                     . '<th colspan="2">TAHUN<br>PEROLEHAN/BUKU</th>';
            case 'F':
                return '<th rowspan="2">KONDISI<br>BANGUNAN<br>(B,RR,RB)</th>'
                     . '<th colspan="3">IDENTITAS</th>'
                     . '<th rowspan="2">BIDANG</th>'
                     . '<th rowspan="2">SUMBER<br>DANA</th>';
            default:
                return '';
        }
    }

    protected function renderKibTableSubHeader(string $kibType, array $fields): string
    {
        switch ($kibType) {
            case 'A':
                return '<th>LUAS (M2)</th><th>TAHUN PEROLEHAN</th><th>BUKU</th><th>ALAMAT</th>'
                     . '<th>HAK</th><th>NOMOR</th><th>TGL</th>';
            case 'B':
                return '<th>MERK/TYPE</th><th>UKURAN/AN/CC</th><th>SAHAN</th><th>TAHUN PEROLEHAN</th><th>BUKU</th>'
                     . '<th>PABRIK</th><th>RANGKA</th><th>MESIN</th><th>POLISI</th><th>BPKB</th>';
            case 'C':
                return '<th>BERTINGKAT AT/TIDAK</th><th>BETON/TIDAK</th>'
                     . '<th>LUAS (M2)</th><th>TAHUN PEROLEHAN</th><th>BUKU</th>'
                     . '<th>TANGGAL</th><th>NOMOR</th><th>ALAMAT</th>';
            case 'D':
                return '<th>PANJANG (KM)</th><th>LEBAR (M)</th><th>LUAS (M2)</th>'
                     . '<th>TAHUN PEROLEHAN</th><th>BUKU</th><th>TGL</th><th>NOMOR</th><th>ALAMAT</th>'
                     . '<th>STATUS</th><th>KODE</th>';
            case 'E':
                return '<th>JUDUL/PENCIPTA</th><th>SPESIFI-KASI</th>'
                     . '<th>ASAL DAERAH</th><th>PENCIPTA</th><th>BAHAN</th>'
                     . '<th>JENIS</th><th>UKURAN</th>'
                     . '<th>PEROLEHAN</th><th>BUKU</th>';
            case 'F':
                return '<th>TAHUN BUKU</th><th>TGL DOKUMEN</th><th>NO DOKUMEN</th>';
            default:
                return '';
        }
    }

    protected function renderKibTableRow(string $kibType, array $fields, Inventaris $inv, string $kondisi): string
    {
        $emptyDash = fn($val) => ($val === null || trim((string)$val) === '') ? '-' : (string)$val;
        
        $kv = function(string $key) use ($inv): string {
            $val = $inv->getKibValue($key);
            if ($val === null || trim((string)$val) === '') {
                return '-';
            }
            return htmlentities((string)$val);
        };
        $kvNum = function(string $key) use ($inv): string {
            $v = $inv->getKibValue($key);
            if ($v === null || trim((string)$v) === '') {
                return '-';
            }
            $f = (float)$v;
            return number_format($f, 2, ',', '.');
        };

        switch ($kibType) {
            case 'A':
                return '<td style="text-align:center;">' . $kvNum('luas_m2') . '</td>'
                     . '<td style="text-align:center;">' . $emptyDash($inv->tahun_perolehan) . '</td>'
                     . '<td style="text-align:center;">' . $kv('tahun_buku') . '</td>'
                     . '<td>' . $kv('alamat') . '</td>'
                     . '<td>' . $kv('hak') . '</td>'
                     . '<td>' . $kv('sertifikat_nomor') . '</td>'
                     . '<td style="text-align:center;">' . $kv('sertifikat_tgl') . '</td>';
            case 'B':
                return '<td>' . $kv('merk_type') . '</td>'
                     . '<td style="text-align:center;">' . $kv('ukuran_cc') . '</td>'
                     . '<td>' . $kv('bahan') . '</td>'
                     . '<td style="text-align:center;">' . $emptyDash($inv->tahun_perolehan) . '</td>'
                     . '<td style="text-align:center;">' . $kv('tahun_buku') . '</td>'
                     . '<td>' . $kv('no_pabrik') . '</td>'
                     . '<td>' . $kv('no_rangka') . '</td>'
                     . '<td>' . $kv('no_mesin') . '</td>'
                     . '<td>' . $kv('no_polisi') . '</td>'
                     . '<td>' . $kv('no_bpkb') . '</td>';
            case 'C':
                return '<td style="text-align:center;">' . $kondisi . '</td>'
                     . '<td>' . $kv('konstruksi_bertingkat') . '</td>'
                     . '<td>' . $kv('konstruksi_beton') . '</td>'
                     . '<td style="text-align:center;">' . $kvNum('luas_m2') . '</td>'
                     . '<td style="text-align:center;">' . $emptyDash($inv->tahun_perolehan) . '</td>'
                     . '<td style="text-align:center;">' . $kv('tahun_buku') . '</td>'
                     . '<td style="text-align:center;">' . $kv('dokumen_tgl') . '</td>'
                     . '<td>' . $kv('dokumen_nomor') . '</td>'
                     . '<td>' . $kv('alamat') . '</td>'
                     . '<td>' . $kv('status_tanah') . '</td>'
                     . '<td>' . $kv('nomor_kode_tanah') . '</td>';
            case 'D':
                return '<td>' . $kv('konstruksi') . '</td>'
                     . '<td style="text-align:center;">' . $kvNum('panjang_km') . '</td>'
                     . '<td style="text-align:center;">' . $kvNum('lebar_m') . '</td>'
                     . '<td style="text-align:center;">' . $kvNum('luas_m2') . '</td>'
                     . '<td style="text-align:center;">' . $emptyDash($inv->tahun_perolehan) . '</td>'
                     . '<td style="text-align:center;">' . $kv('tahun_buku') . '</td>'
                     . '<td style="text-align:center;">' . $kv('dokumen_tgl') . '</td>'
                     . '<td>' . $kv('dokumen_nomor') . '</td>'
                     . '<td>' . $kv('alamat') . '</td>'
                     . '<td>' . $kv('tanah_status') . '</td>'
                     . '<td>' . $kv('tanah_kode') . '</td>';
            case 'E':
                $tahunPerolehan = $emptyDash($inv->tahun_perolehan);
                $tahunBuku = $kv('tahun_buku');
                return '<td>' . $kv('judul_pencipta') . '</td>'
                     . '<td>' . $kv('spesifikasi') . '</td>'
                     . '<td>' . $kv('asal_daerah') . '</td>'
                     . '<td>' . $kv('pencipta_kesenian') . '</td>'
                     . '<td>' . $kv('bahan') . '</td>'
                     . '<td>' . $kv('jenis_hewan') . '</td>'
                     . '<td>' . $kv('ukuran') . '</td>'
                     . '<td style="text-align:center;">' . $tahunPerolehan . '</td>'
                     . '<td style="text-align:center;">' . $tahunBuku . '</td>';
            case 'F':
                return '<td style="text-align:center;">' . $kondisi . '</td>'
                     . '<td style="text-align:center;">' . $kv('tahun_buku') . '</td>'
                     . '<td style="text-align:center;">' . $kv('dokumen_tgl') . '</td>'
                     . '<td>' . $kv('dokumen_nomor') . '</td>'
                     . '<td>' . $kv('bidang') . '</td>'
                     . '<td>' . $kv('sumber_dana') . '</td>';
            default:
                return '';
        }
    }

    protected function getJumlahColSpan(string $kibType, array $fields): int
    {
        // NO + Nama + Kode + REG + kolom khusus (tanpa Harga, Kondisi, Keterangan)
        $base = ['A'=>12,'B'=>15,'C'=>16,'D'=>16,'E'=>15,'F'=>11];
        return $base[$kibType] ?? 5;
    }

    protected function renderTtd(?array $pejabatKiri, ?array $pejabatKanan, bool $includeImg, string $tanggalFormatted): string
    {
        $kiriNama  = trim((string)($pejabatKiri['nama'] ?? ''));
        $kananNama = trim((string)($pejabatKanan['nama'] ?? ''));
        if ($kiriNama === '' && $kananNama === '') {
            return '';
        }

        $html = '<table class="ttd-table"><tr>';

        // Kiri: Sekretaris Desa
        $html .= '<td class="ttd-kiri">';
        $html .= '<div class="ttd-jabatan">MENGETAHUI</div>';
        $html .= '<div class="ttd-jabatan">SEKRETARIS DESA</div>';
        $html .= '<div class="ttd-tugas">Selaku Pembantu Pengelola Barang Milik Desa</div>';
        if ($includeImg && !empty($pejabatKiri['ttd_path'])) {
            $ttdPath = storage_path('app/public/' . ltrim($pejabatKiri['ttd_path'], '/'));
            if (file_exists($ttdPath)) {
                $imgData = base64_encode(file_get_contents($ttdPath));
                $mime = mime_content_type($ttdPath) ?: 'image/png';
                $html .= '<img src="data:' . $mime . ';base64,' . $imgData . '" style="max-height:50px;max-width:120px;display:block;margin:0 auto 4px;" />';
            }
        }
        $html .= '<div class="ttd-nama">' . htmlentities($pejabatKiri['nama'] ?? '') . '</div>';
        $html .= '</td>';

        // Kanan: Petugas Barang
        $html .= '<td class="ttd-kanan">';
        if ($tanggalFormatted) {
            $html .= '<div style="font-size:9px;margin-bottom:4px;">' . htmlentities($tanggalFormatted) . '</div>';
        }
        $html .= '<div class="ttd-jabatan">PETUGAS / PENGURUS BARANG MILIK DESA</div>';
        $html .= '<div class="ttd-tugas">&nbsp;</div>';
        if ($includeImg && !empty($pejabatKanan['ttd_path'])) {
            $ttdPath = storage_path('app/public/' . ltrim($pejabatKanan['ttd_path'], '/'));
            if (file_exists($ttdPath)) {
                $imgData = base64_encode(file_get_contents($ttdPath));
                $mime = mime_content_type($ttdPath) ?: 'image/png';
                $html .= '<img src="data:' . $mime . ';base64,' . $imgData . '" style="max-height:50px;max-width:120px;display:block;margin:0 auto 4px;" />';
            }
        }
        $html .= '<div class="ttd-nama">' . htmlentities($pejabatKanan['nama'] ?? '') . '</div>';
        $html .= '</td>';

        $html .= '</tr></table>';
        return $html;
    }

    protected function savePdf(string $html, string $relativePath, string $paperSize, bool $isFullDoc = false): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        if (!$isFullDoc) {
            $pageSizeCss = $this->pageSizeCss($paperSize);
            $html = '<!doctype html><html><head><meta charset="utf-8">'
                . '<style>@page{size:' . $pageSizeCss . ';margin:10mm 8mm 22mm 8mm;}'
                . 'body{font-family:Arial,sans-serif;font-size:9px;color:#000;margin:0;}</style>'
                . '</head><body>' . $html . '</body></html>';
        }

        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            [$size, $orientation] = $this->resolvePaperSize($paperSize);
            $pdf->setPaper($size, $orientation);
            Storage::disk($this->disk)->put($relativePath, $pdf->output());
        } elseif (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
            $dompdf->loadHtml($html);
            [$size, $orientation] = $this->resolvePaperSize($paperSize);
            $dompdf->setPaper($size, $orientation);
            $dompdf->render();
            Storage::disk($this->disk)->put($relativePath, $dompdf->output());
        } else {
            Storage::disk($this->disk)->put($relativePath, $html);
        }
    }

    protected function saveExcel(
        string $kibType, array $kibConfig,
        \Illuminate\Support\Collection $inventarisList,
        ?array $pejabatKiri, ?array $pejabatKanan,
        bool $includeImg, string $tanggalDokumen, string $keteranganDokumen,
        array $filters, string $relativePath
    ): void {
        $sheet = new \App\Exports\Kib\KibSheetExport(
            $kibType, $kibConfig, $inventarisList,
            $pejabatKiri, $pejabatKanan, $includeImg,
            $tanggalDokumen, $keteranganDokumen, $filters
        );
        \Maatwebsite\Excel\Facades\Excel::store($sheet, $relativePath, $this->disk);
    }

    protected function getKibPdfCss(string $paperSize): string
    {
        $pageSizeCss = $this->pageSizeCss($paperSize);
        return '
            @page { size: ' . $pageSizeCss . '; margin: 10mm 8mm 22mm 8mm; }
            body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; }
            .doc-title { text-align: center; font-size: 13px; font-weight: bold; margin: 0 0 8px 0; }
            .doc-subtitle { text-align: center; font-size: 11px; margin: 0 0 45px 0; }
            .info-table { width: 50%; border-collapse: collapse; margin-top: 14px; margin-bottom: 14px; font-size: 9px; }
            .info-table td { padding: 2px 4px; border: none; vertical-align: top; font-weight: bold; }
            .info-table td:first-child { width: 90px; }
            .info-table td:nth-child(2) { width: 10px; text-align: center; }
            table.inv-table { width: 100%; border-collapse: collapse; font-size: 8px; }
            table.inv-table th { background: #fff; font-weight: bold; text-align: center;
                border: 1px solid #000; padding: 3px 2px; vertical-align: middle; font-size: 8px; }
            table.inv-table td { border: 1px solid #000; padding: 3px 2px; vertical-align: middle; font-size: 8px; }
            .no-col { text-align: center; width: 20px; }
            .jumlah-row td { font-weight: bold; text-align: center; }
            .ttd-table { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
            .ttd-table td { border: none; vertical-align: top; padding: 0; }
            .ttd-kiri { width: 50%; text-align: center; }
            .ttd-kanan { width: 50%; text-align: center; }
            .ttd-jabatan { font-size: 9px; font-weight: bold; margin-bottom: 2px; }
            .ttd-tugas { font-size: 8px; margin-bottom: 40px; }
            .ttd-nama { font-size: 9px; font-weight: bold; text-decoration: underline; }
            .page-break { page-break-after: always; }
        ' . \App\Services\ExportService::buildFooterCss();
    }

    protected function pageSizeCss(string $paperSize): string
    {
        return match(strtolower($paperSize)) {
            'letter' => 'letter landscape',
            'f4'     => '21cm 33cm landscape',
            default  => 'legal landscape',
        };
    }

    protected function resolvePaperSize(string $paperSize): array
    {
        return match(strtolower($paperSize)) {
            'letter' => ['letter', 'landscape'],
            'f4'     => [[0, 0, 595.28, 935.43], 'landscape'],
            default  => ['legal', 'landscape'],
        };
    }
}
