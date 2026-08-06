<?php

namespace App\Services;

use App\Models\ExportLog;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use App\Models\Inventaris;
use App\Exports\InventarisExport;
use App\Exports\BukuInventarisSheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ExportService
{
    protected $disk = 'local';

    /**
     * Generate CSS footer yang selalu di tepi bawah kertas (fixed position).
     * Dipakai oleh semua export PDF agar konsisten.
     *
     * @param string $namaJenis  Nama jenis inventaris/dokumen, contoh: "Aset Tetap Lainnya"
     * @return string CSS string siap pakai
     */
    public static function buildFooterCss(string $namaJenis = ''): string
    {
        return '';
    }

    public static function buildFooterHtml(string $label): string
    {
        $labelJs = addslashes($label);
        return '
            <span style="font-weight: normal; text-decoration: none; font-style: normal; display: none;"></span>
            <script type="text/php">
                if (isset($pdf)) {
                    $w     = $pdf->get_width();
                    $h     = $pdf->get_height();
                    $font  = $fontMetrics->getFont("helvetica", "normal");
                    $size  = 9.5;
                    $col   = [0, 0, 0];
                    $yLine = $h - 20;
                    $yText = $h - 36;

                    // Garis pakai underscore via page_text agar muncul di setiap halaman
                    $underscoreWidth = $fontMetrics->getTextWidth("_", $font, 9);
                    $numChars = (int)(($w - 40) / $underscoreWidth);
                    $lineText = str_repeat("_", $numChars);
                    $pdf->page_text(20, $yLine, $lineText, $font, 9, $col);

                    // Teks footer kiri dan kanan
                    $pdf->page_text(20, $yText, "' . $labelJs . '", $font, $size, $col);
                    $rightText = "Halaman : {PAGE_NUM} dari {PAGE_COUNT}";
                    $tw = $fontMetrics->getTextWidth("Halaman : 1 dari 1", $font, $size);
                    $pdf->page_text($w - $tw - 20, $yText, $rightText, $font, $size, $col);
                }
            </script>
        ';
    }

    protected function exportFieldLabel($field): string
    {
        $label = trim((string) ($field->label_field ?? ''));

        if (\App\Models\JenisInventarisData::isCurrencyField(
            $field->fixed_field_name ?? null,
            $field->tipe ?? null,
            $field->nama_field ?? null,
            $field->label_field ?? null
        )) {
            $normalized = strtoupper(str_replace([' ', '(', ')'], '', $label));
            if (str_contains($normalized, 'RP')) return $label;
            return $label !== '' ? $label . ' (Rp)' : '(Rp)';
        }

        $tipe = $field->tipe ?? null;
        if ($tipe === 'decimal_m2') {
            $normalized = strtoupper(str_replace([' ', '²'], '', $label));
            if (str_contains($normalized, 'M2')) return preg_replace('/[mM]\s*(?:2|²)/u', 'M²', $label) ?? $label;
            return $label !== '' ? $label . ' (M²)' : 'M²';
        }
        if ($tipe === 'decimal_km') {
            $upper = strtoupper($label);
            if (str_contains($upper, '(KM)') || str_contains($upper, ' KM')) return $label;
            return $label !== '' ? $label . ' (Km)' : 'Km';
        }
        if ($tipe === 'decimal_m') {
            if (preg_match('/\(M\)/i', $label) && !str_contains($label, '²') && !str_contains(strtoupper($label), 'M2')) return $label;
            return $label !== '' ? $label . ' (M)' : 'M';
        }

        return $label;
    }

    protected function buildRows($jenis, array $filters = [], bool $isOrphan = false): array
    {
        // Get fields ordered by order — hanya yang include_in_export, skip jenis_barang
        $fields = $isOrphan ? collect([]) : $jenis->fields()
            ->orderBy('order', 'asc')
            ->where('include_in_export', '!=', 0)
            ->where(function($q) {
                $q->where('fixed_field_name', '!=', 'jenis_barang')
                  ->orWhereNull('fixed_field_name');
            })
            ->get()
            ->filter(fn($f) => ($f->nama_field ?? null) !== 'jenis_barang')
            ->values();

        // Build header - No column always first, then follow field order
        $header = ['No'];
        foreach ($fields as $field) {
            // Rename nama_barang → "Jenis Barang / Nama Barang"
            if (($field->fixed_field_name ?? null) === 'nama_barang') {
                $header[] = 'Jenis Barang / Nama Barang';
            } else {
                $header[] = $this->exportFieldLabel($field);
            }
        }

        // Get inventaris data with eager loading — hanya yang sudah disetujui
        if ($isOrphan) {
            // Orphan inventaris: jenis_inventaris_id = NULL
            $query = Inventaris::where('jenis_inventaris_id', null)
                ->with(['jenis', 'values.field']);
        } else {
            $query = Inventaris::where('jenis_inventaris_id', $jenis->id)
                ->with(['jenis', 'values.field']);
        }
        
        if (isset($filters['tahun'])) {
            $query->where('tahun_perolehan', '<=', $filters['tahun']);
        }

        $inventaris = $query->get();

        // Build data rows
        $rows = [];
        $no = 1;
        foreach ($inventaris as $inv) {
            $row = [$no++]; // No column
            
            // Loop through fields in order
            foreach ($fields as $field) {
                $value = $field->getValueFromInventaris($inv);
                $row[] = $value;
            }
            
            $rows[] = $row;
        }

        return ['header' => $header, 'rows' => $rows];
    }

    /**
     * Build rows untuk virtual jenis (aggregated dari semua real jenis)
     * Digunakan untuk export Buku Inventaris Aset Desa
     */
    protected function buildRowsAggregated($jenis, array $filters = []): array
    {
        // Get all fields dari semua real jenis (unique by id, sorted by order dalam masing-masing jenis)
        $realJenis = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                                    ->with(['fields' => function($q){ $q->orderBy('order'); }])
                                    ->orderBy('nama')
                                    ->get();
        
        // Aggregate fields dengan deduplication by field id
        $allFields = collect();
        $fieldIds = [];
        foreach ($realJenis as $real) {
            foreach ($real->fields->sortBy('order') as $field) {
                // Add only if belum ada
                if (!in_array($field->id, $fieldIds)) {
                    $allFields->push($field);
                    $fieldIds[] = $field->id;
                }
            }
        }
        $allFields = $allFields->sortBy('order');
        
        // Build header
        $header = ['No'];
        foreach ($allFields as $field) {
            $header[] = $this->exportFieldLabel($field);
        }

        // Query semua inventaris dari real jenis, sorted by jenis then by nomor urut
        $query = Inventaris::whereIn('jenis_inventaris_id', $realJenis->pluck('id'))
                   ->with(['jenis', 'values.field'])
                   ->orderBy('jenis_inventaris_id')
                   ->orderBy('created_at');
        
        if (isset($filters['tahun'])) {
            $query->where('tahun_perolehan', '<=', $filters['tahun']);
        }

        $inventaris = $query->get();

        // Build rows
        $rows = [];
        $no = 1;
        foreach ($inventaris as $inv) {
            $row = [$no++];
            foreach ($allFields as $field) {
                $value = $field->getValueFromInventaris($inv);
                $row[] = $value;
            }
            $rows[] = $row;
        }

        return ['header' => $header, 'rows' => $rows];
    }

    protected function renderHtmlAll($allJenis, array $filters = [], string $prependHtml = '', bool $includeImg = true, $pejabatOverride = null, string $paperSize = 'legal'): string
    {
        $pageSizeCss = match(strtolower($paperSize)) {
            'letter' => 'letter landscape',
            'f4'     => '21cm 33cm landscape',
            default  => 'legal landscape',
        };

        $css = '
            @page { size: ' . $pageSizeCss . '; margin: 1.5cm 1.5cm 2.2cm 1.5cm; }
            body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 0; }
            .doc-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 1px; }
            .doc-subtitle { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 16px; }
            .doc-periode { text-align: center; font-size: 11px; font-weight: normal; margin-bottom: 55px; }
            .info-outer { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .info-outer td { border: none; vertical-align: top; padding: 0; }
            .info-left { width: 62%; }
            .info-right { width: 38%; padding-left: 0; }
            .info-table { width: 100%; border-collapse: collapse; font-size: 11px; }
            .info-table td { padding: 1px 3px; border: none; vertical-align: top; }
            .info-table td:first-child { width: 95px; font-weight: bold; }
            .info-table td:nth-child(2) { width: 10px; text-align: center; font-weight: bold; }
            .info-table td:nth-child(3) { font-weight: bold; }
            table.inv-table { border-collapse: collapse; width: 100%; table-layout: auto; }
            table.inv-table th { background: #ffffff; font-size: 9px; font-weight: bold; text-align: center;
                 border: 1px solid #000000; padding: 3px 2px; vertical-align: middle; }
            table.inv-table td { border: 1px solid #000000; padding: 3px 3px; font-size: 9px; vertical-align: middle; }
            .no-col { text-align: center; width: 25px; }
            .kondisi-col { text-align: center; width: 55px; }
            .col-num td { text-align: center; font-weight: bold; border: 1px solid #000000; padding: 2px 3px; font-size: 11px; }
            .jumlah-row td { font-weight: bold; border: 1px solid #000000; padding: 3px 4px; font-size: 11px; }
            .petunjuk { font-size: 11px; color: #333; }
            .petunjuk-title { font-weight: bold; margin-bottom: 3px; }
            .footer-table { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
            .footer-table td { vertical-align: top; padding: 0; border: none; }
            .footer-table > tbody > tr > td { border: none; }
            .footer-petunjuk { width: 55%; font-size: 11px; color: #333; }
            .footer-ttd { width: 45%; padding-left: 20px; }
            .ttd-title { font-size: 12px; font-weight: bold; text-align: center; margin-bottom: 8px; }
            .footer-ttd > table { width: 100%; border-collapse: collapse; }
            .footer-ttd > table td { border: none; padding: 0; vertical-align: bottom; }
            .footer-ttd > table > tbody > tr > td:first-child { width: 50%; text-align: left; }
            .footer-ttd > table > tbody > tr > td:last-child { width: 50%; text-align: center; }
            .footer-ttd > table > tbody > tr > td table { border-collapse: collapse; width: 100%; }
            .footer-ttd > table > tbody > tr > td table td { border: none; padding: 0; }
            .ttd-img { max-width: 80px; max-height: 35px; display: block; margin: 0 auto 2px auto; }
            .ttd-img-gambar { max-width: 160px; max-height: 80px; display: block; margin: 0 auto 2px auto; }
            .page-break { page-break-after: always; }
            .footer-line-separator { 
                width: 100%; 
                height: 0; 
                border: none; 
                border-bottom: 1.5px solid #000000; 
                margin: 0; 
                padding: 0;
            }
        ';

        $html  = '<!doctype html><html><head><meta charset="utf-8">';
        $html .= '<title>ASET DESA - SEMUA JENIS</title>';
        $html .= '<style>' . $css . '</style></head><body>';
        if ($prependHtml !== '') {
            $html .= $prependHtml;
            $html .= '<div class="page-break"></div>';
        }

        $totalJenis = count($allJenis);
        $jenisIndex = 0;
        $renderedCount = 0;
        foreach ($allJenis as $jenis) {
            // Lewati jenis virtual "Buku Inventaris Aset Desa" — sudah dirender sebagai halaman pertama
            if (stripos($jenis->nama, 'buku inventaris') !== false) {
                continue;
            }

            $rows = $this->buildRows($jenis, $filters);

            // Skip jenis yang tidak punya data — tidak render halaman kosong
            if (empty($rows['rows'])) {
                continue;
            }

            // Load pejabat: prioritas override dari UI → fallback defaults DB
            $pejabatSnapshot = [];
            if (is_array($pejabatOverride) && count($pejabatOverride) > 0) {
                foreach ($pejabatOverride as $pid) {
                    $pe = Pejabat::find($pid);
                    if ($pe) {
                        $pejabatSnapshot[] = $pe->toArray();
                    }
                }
            } else {
                $pejabatSnapshot = $jenis->pejabatDefaults()->with('pejabat')->orderBy('order', 'asc')
                    ->get()
                    ->map(fn($d) => $d->pejabat ? $d->pejabat->toArray() : null)
                    ->filter()
                    ->values()
                    ->toArray();
            }

            // Page break sebelum jenis (kecuali yang pertama dirender)
            if ($renderedCount > 0) {
                $html .= '<div class="page-break"></div>';
            }

            // Use renderHtml in bodyOnly mode — same layout as standalone PDF per jenis
            $html .= $this->renderHtml($jenis, $rows, $pejabatSnapshot, $includeImg, true, '', '', $filters, $paperSize);

            $renderedCount++;
            $jenisIndex++;
        }

        // Footer: text di atas, garis di bawah (sama seperti gambar user)
        $html .= '
            <span style="font-weight: normal; text-decoration: none; font-style: normal; display: none;"></span>
            <script type="text/php">
                if (isset($pdf)) {
                    $w = $pdf->get_width();
                    $h = $pdf->get_height();
                    $font = $fontMetrics->getFont("helvetica", "normal");
                    $size = 9.5;
                    $col = [0, 0, 0];
                    
                    // Posisi: text di atas, garis di bawah
                    $yText = $h - 36;
                    $yLine = $h - 20;
                    
                    // Render footer text dulu (di atas)
                    $pdf->page_text(20, $yText, "Laporan Hasil Inventaris Aset Desa - Semua Jenis", $font, $size, $col);
                    $rightText = "Halaman : {PAGE_NUM} dari {PAGE_COUNT}";
                    $tw = $fontMetrics->getTextWidth("Halaman : 1 dari 1", $font, $size);
                    $rightTextX = $w - $tw - 20;
                    $pdf->page_text($rightTextX, $yText, $rightText, $font, $size, $col);
                    
                    // Render garis: hitung berapa character yang pas antara margin kiri sampai margin kanan
                    // Gunakan actual text width untuk menghitung panjang garis yang tepat
                    $lineStartX = 20;
                    $lineEndX = $w - 20;
                    $availableWidth = $lineEndX - $lineStartX;
                    
                    // Buat string underscore sepanjang available width
                    // Karakter underscore di font size 9 sekitar 4-5 pixel
                    $underscoreWidth = $fontMetrics->getTextWidth("_", $font, 9);
                    $numChars = (int)($availableWidth / $underscoreWidth);
                    $lineText = str_repeat("_", $numChars);
                    $pdf->page_text($lineStartX, $yLine, $lineText, $font, 9, $col);
                }
            </script>
        ';
        
        $html .= '</body></html>';

        return $html;
    }

    protected function renderHtml(JenisInventaris $jenis, array $data, array $pejabatSnapshot = [], bool $includeSignatureImage = true, bool $bodyOnly = false, string $tanggalDokumen = '', string $keteranganDokumen = '', array $filters = [], string $paperSize = 'legal', string $docLampiran = '', string $docBaNomor = '', string $docBaTanggal = ''): string
    {
        $desa = config('desa');

        $bulanMap = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                     7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        // Periode Tanggal = 31 Desember dari tahun filter (bukan dari tanggal dokumen)
        $tahunFilter = (int)($filters['tahun'] ?? date('Y'));
        if (!$tahunFilter) $tahunFilter = (int)date('Y');
        $periodeStr = 'Periode Tanggal, 31 Desember ' . $tahunFilter;

        // Tanggal Berita Acara = dari input tanggal dokumen user (default: hari ini)
        $tanggalBeritaAcara = '';
        if ($tanggalDokumen) {
            try {
                $dt = \Carbon\Carbon::parse($tanggalDokumen);
                $tanggalBeritaAcara = $dt->day . ' ' . $bulanMap[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {}
        }
        if (!$tanggalBeritaAcara) {
            $now = now();
            $tanggalBeritaAcara = $now->day . ' ' . $bulanMap[$now->month] . ' ' . $now->year;
        }

        // Format Tanggal BA dari input (untuk ditampilkan di header)
        $tanggalBaFormatted = '';
        if ($docBaTanggal) {
            try {
                $dt = \Carbon\Carbon::parse($docBaTanggal);
                $tanggalBaFormatted = $dt->day . ' ' . $bulanMap[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {}
        }

        // Determine which column index is 'kondisi' (0-based, after No column)
        // Gunakan fields yang sama dengan buildRows: include_in_export, skip jenis_barang
        $fields = $jenis->fields()
            ->orderBy('order', 'asc')
            ->where('include_in_export', '!=', 0)
            ->where(function($q) {
                $q->where('fixed_field_name', '!=', 'jenis_barang')
                  ->orWhereNull('fixed_field_name');
            })
            ->get()
            ->filter(fn($f) => ($f->nama_field ?? null) !== 'jenis_barang')
            ->values();
        $kondisiIdx = -1;
        $idx        = 1; // 0 = No column
        foreach ($fields as $field) {
            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                $kondisiIdx = $idx;
                break;
            }
            $idx++;
        }

        // Deteksi grup NOMOR IDENTITAS lebih awal (agar loop visual menghitung sebagai 1 kolom)
        // 4 field berurutan dengan label mengandung polisi/mesin/rangka/bpkb
        $niFieldStart = -1; // index field (0-based) kolom pertama grup
        $keywordsNi = ['polisi', 'mesin', 'rangka', 'bpkb'];
        $fieldsArr  = $fields->values();
        for ($fi = 0; $fi <= $fieldsArr->count() - 4; $fi++) {
            $found = [];
            for ($fj = $fi; $fj < $fi + 4; $fj++) {
                $lbl = mb_strtolower(trim((string)($fieldsArr[$fj]->label_field ?? '')));
                foreach ($keywordsNi as $kw) {
                    if (str_contains($lbl, $kw)) { $found[$kw] = $fj; break; }
                }
            }
            if (count($found) === 4) { $niFieldStart = $fi; break; }
        }
        $hasNomorIdentitasEarly = $niFieldStart >= 0;

        // Hitung posisi kolom nilai dan total visual kolom untuk baris Jumlah
        $nilaiRowIdx    = -1;  // index di $data['rows'] untuk kolom sum-target (pertama yang ditemukan)
        $nilaiVisualIdx = -1;  // index visual di HTML (kondisi melebar jadi 3)
        $currencyRowIndices = []; // semua indeks kolom mata uang (untuk format Rp di data rows)
        $m2RowIndices = []; // semua indeks kolom bertipe decimal_m2, decimal_km, decimal_m
        $centerRowIndices = []; // kolom yang di-center: kode_barang, nup, tahun_perolehan
        $nupRowIdx = -1; // index kolom NUP untuk min-width
        $fRowIdx    = 1;  // 0 = kolom No
        $fVisualIdx = 1;
        foreach ($fields as $fIndex => $field) {
            $isKondisi = ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi');
            if ($isKondisi) {
                $fRowIdx++;
                $fVisualIdx += 3;
                continue;
            }
            // NOMOR IDENTITAS: 4 field jadi 1 kolom visual.
            // Field ke-2,3,4 grup: naikkan fRowIdx tapi JANGAN naikkan fVisualIdx
            if ($hasNomorIdentitasEarly && $fIndex > $niFieldStart && $fIndex <= $niFieldStart + 3) {
                $fRowIdx++;
                continue;
            }
            // Track NUP index
            if (($field->fixed_field_name ?? null) === 'nup') {
                $nupRowIdx = $fRowIdx;
            }
            // Kolom yang di-center: kode_barang, nup, tahun_perolehan + semua tipe angka kecuali currency
            $isNumericTipe = in_array($field->tipe ?? null, ['number', 'decimal_km', 'decimal_m', 'decimal_m2', 'year'], true);
            $isCurrencyCol = \App\Models\JenisInventarisData::isCurrencyField(
                $field->fixed_field_name ?? null,
                $field->tipe ?? null,
                $field->nama_field ?? null,
                $field->label_field ?? null
            );
            if (
                in_array($field->fixed_field_name ?? null, ['kode_barang', 'nup', 'tahun_perolehan'], true)
                || ($isNumericTipe && !$isCurrencyCol)
            ) {
                $centerRowIndices[] = $fRowIdx;
            }
            // Cek apakah ini kolom mata uang
            $isNilai = \App\Models\JenisInventarisData::isCurrencyField(
                $field->fixed_field_name ?? null,
                $field->tipe ?? null,
                $field->nama_field ?? null,
                $field->label_field ?? null
            );
            if ($isNilai) {
                $currencyRowIndices[] = $fRowIdx;
                if ($nilaiRowIdx < 0) {
                    $nilaiRowIdx    = $fRowIdx;
                    $nilaiVisualIdx = $fVisualIdx;
                }
            } elseif (in_array($field->tipe ?? null, ['decimal_m2', 'decimal_km', 'decimal_m'], true)) {
                $m2RowIndices[] = $fRowIdx;
            }
            $fRowIdx++;
            $fVisualIdx++;
        }
        $totalVisualCols = $fVisualIdx; // total kolom HTML setelah semua field

        // Jumlahkan nilai dari semua baris data
        $nilaiTotal = 0.0;
        if ($nilaiRowIdx >= 0) {
            foreach ($data['rows'] as $row) {
                $raw   = (string)($row[$nilaiRowIdx] ?? '');
                $clean = preg_replace('/[^0-9,.]/', '', $raw);
                if ($clean === '') continue;
                // Format Indonesian: 1.000.000 → strip dots, atau 1,5 → 1.5
                if (substr_count($clean, '.') > 1) {
                    $clean = str_replace('.', '', $clean); // ribuan bertitik
                } elseif (str_contains($clean, ',')) {
                    $clean = str_replace(['.', ','], ['', '.'], $clean);
                }
                $nilaiTotal += is_numeric($clean) ? (float)$clean : 0.0;
            }
        }

        $pageSizeCss = match(strtolower($paperSize)) {
            'letter' => 'letter landscape',
            'f4'     => '21cm 33cm landscape',
            default  => 'legal landscape',
        };

        $css = '
            @page { size: ' . $pageSizeCss . '; margin: 1.5cm 1.5cm 2.2cm 1.5cm; }
            body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 0; }
            .doc-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 1px; }
            .doc-subtitle { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 16px; }
            .doc-periode { text-align: center; font-size: 11px; font-weight: normal; margin-bottom: 55px; }
            .info-outer { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .info-outer td { border: none; vertical-align: top; padding: 0; }
            .info-left { width: 62%; }
            .info-right { width: 38%; padding-left: 0; }
            .info-table { width: 100%; border-collapse: collapse; font-size: 11px; }
            .info-table td { padding: 1px 3px; border: none; vertical-align: top; }
            .info-table td:first-child { width: 95px; font-weight: bold; }
            .info-table td:nth-child(2) { width: 10px; text-align: center; font-weight: bold; }
            .info-table td:nth-child(3) { font-weight: bold; }
            table.inv-table { border-collapse: collapse; width: 100%; table-layout: auto; }
            table.inv-table th { background: #ffffff; font-size: 9px; font-weight: bold; text-align: center;
                 border: 1px solid #000000; padding: 3px 2px; vertical-align: middle; }
            table.inv-table td { border: 1px solid #000000; padding: 3px 3px; font-size: 9px;
                 vertical-align: middle; }
            .no-col { text-align: center; width: 25px; }
            .kondisi-col { text-align: center; width: 55px; }
            .col-num td { text-align: center; font-weight: bold; border: 1px solid #000000; padding: 2px 3px; font-size: 11px; }
            .jumlah-row td { font-weight: bold; border: 1px solid #000000; padding: 3px 4px; font-size: 11px; }
            .petunjuk { font-size: 11px; color: #333; }
            .petunjuk-title { font-weight: bold; margin-bottom: 3px; }
            .footer-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
            .footer-table td { vertical-align: top; padding: 0; border: none; }
            .footer-petunjuk { width: 55%; font-size: 11px; color: #333; }
            .footer-ttd { width: 45%; padding-left: 20px; }
            .ttd-title { font-size: 12px; font-weight: bold; text-align: center; margin-bottom: 8px; }
            .ttd-img { max-width: 80px; max-height: 35px; display: block; margin: 0 auto 2px auto; }
            .ttd-img-gambar { max-width: 160px; max-height: 80px; display: block; margin: 0 auto 2px auto; }
        ' . self::buildFooterCss();

        if ($bodyOnly) {
            // bodyOnly mode: format LENGKAP tanpa HTML wrapper (untuk digabung di renderHtmlAll)
            $html  = '<div class="doc-title">LAPORAN HASIL INVENTARIS ASET DESA</div>';
            $html .= '<div class="doc-subtitle">BERUPA ' . strtoupper(htmlentities($jenis->nama)) . '</div>';
            $html .= '<div class="doc-periode">' . htmlentities($periodeStr) . '</div>';
        } else {
            $html  = '<!doctype html><html><head><meta charset="utf-8">';
            $html .= '<title>Laporan Hasil Inventaris Aset Desa - ' . htmlentities($jenis->nama) . '</title>';
            $html .= '<style>' . $css . '</style></head><body>';
            $html .= '<div class="doc-title">LAPORAN HASIL INVENTARIS ASET DESA</div>';
            $html .= '<div class="doc-subtitle">BERUPA ' . strtoupper(htmlentities($jenis->nama)) . '</div>';
            $html .= '<div class="doc-periode">' . htmlentities($periodeStr) . '</div>';
        }

        // Info desa — 2 kolom: kiri (identitas desa) + kanan (lampiran/berita acara)
        $html .= '<table class="info-outer"><tr>';

        // Kolom kiri
        $html .= '<td class="info-left"><table class="info-table">';
        $html .= '<tr><td>Provinsi</td><td>:</td><td>' . htmlentities(($desa['provinsi_kode'] ?? '') . ($desa['provinsi_kode'] && $desa['provinsi_nama'] ? '.  ' : '') . ($desa['provinsi_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Kab./Kota</td><td>:</td><td>' . htmlentities(($desa['kabupaten_kode'] ?? '') . ($desa['kabupaten_kode'] && $desa['kabupaten_nama'] ? '.  ' : '') . ($desa['kabupaten_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Kecamatan</td><td>:</td><td>' . htmlentities(($desa['kecamatan_kode'] ?? '') . ($desa['kecamatan_kode'] && $desa['kecamatan_nama'] ? '.  ' : '') . ($desa['kecamatan_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>Desa</td><td>:</td><td>' . htmlentities(($desa['kode_lokasi'] ?? '') . ($desa['kode_lokasi'] && $desa['desa_nama'] ? '.  ' : '') . ($desa['desa_nama'] ?? '')) . '</td></tr>';
        $html .= '<tr><td>UPB</td><td>:</td><td>' . htmlentities($desa['upb'] ?? '') . '</td></tr>';
        $html .= '<tr><td>No. Kode Lokasi</td><td>:</td><td>' . htmlentities(($desa['kode_lokasi'] ?? '') . ($desa['kode_lokasi'] ? '.' : '')) . '</td></tr>';
        $html .= '</table></td>';

        // Kolom kanan
        $html .= '<td class="info-right"><table class="info-table">';
        $html .= '<tr><td>Lampiran</td><td>:</td><td>' . htmlentities($docLampiran ?: '-') . '</td></tr>';
        $html .= '<tr><td colspan="3" style="padding-top:4px;"><strong>Berita Acara Inventarisasi Aset Desa</strong></td></tr>';
        $html .= '<tr><td>Nomor</td><td>:</td><td>' . htmlentities($docBaNomor ?: '-') . '</td></tr>';
        $html .= '<tr><td>Tanggal</td><td>:</td><td>' . htmlentities($tanggalBaFormatted ?: $tanggalBeritaAcara) . '</td></tr>';
        $html .= '</table></td>';

        $html .= '</tr></table>';

        // Deteksi apakah ini jenis inventaris Jalan, Irigasi dan Jaringan (kib_type = D)
        // Jika ya, kolom decimal_km, decimal_m, decimal_m2 dikelompokkan di bawah header "UKURAN"
        $isJalanIrigasi = ($jenis->kib_type ?? null) === 'D';
        $ukuranIndices = []; // [ci => 'km'|'m'|'m2']
        if ($isJalanIrigasi) {
            foreach ($fields as $fi => $field) {
                $tipe = $field->tipe ?? null;
                $headerIdx = $fi + 1; // +1 karena header[0] = 'No'
                if ($tipe === 'decimal_km')  $ukuranIndices[$headerIdx] = 'km';
                elseif ($tipe === 'decimal_m')  $ukuranIndices[$headerIdx] = 'm';
                elseif ($tipe === 'decimal_m2') $ukuranIndices[$headerIdx] = 'm2';
            }
        }
        $hasUkuran = count($ukuranIndices) === 3;

        // Deteksi grup NOMOR IDENTITAS: 4 field berurutan dengan label mengandung polisi/mesin/rangka/bpkb
        // Jika ditemukan, 4 kolom digabung jadi 1 kolom dengan header 2 baris dan data vertikal
        $nomorIdentitasStartIdx = -1; // index di $data['header'] (0-based) kolom pertama dari grup
        $nomorIdentitasRowIndices = []; // [rowIdx] ke-4 field di $data['rows']
        $nomorIdentitasLabels = []; // label ke-4 field
        $keywords = ['polisi', 'mesin', 'rangka', 'bpkb'];
        $fieldList = $fields->values();
        $fieldCount = $fieldList->count();
        for ($fi = 0; $fi <= $fieldCount - 4; $fi++) {
            $found = [];
            for ($fj = $fi; $fj < $fi + 4; $fj++) {
                $lbl = mb_strtolower(trim((string)($fieldList[$fj]->label_field ?? '')));
                $matched = null;
                foreach ($keywords as $kw) {
                    if (str_contains($lbl, $kw)) { $matched = $kw; break; }
                }
                if ($matched !== null) $found[$matched] = $fj;
            }
            // Semua 4 keyword ditemukan dalam 4 field berurutan
            if (count($found) === 4) {
                $nomorIdentitasStartIdx = $fi + 1; // +1 karena header[0] = 'No'
                foreach (array_values($found) as $k => $fj) {
                    $nomorIdentitasRowIndices[] = $fj + 1; // +1 karena row[0] = No
                    $nomorIdentitasLabels[] = trim((string)($fieldList[$fj]->label_field ?? ''));
                }
                break;
            }
        }
        $hasNomorIdentitas = $nomorIdentitasStartIdx >= 0;

        // Build table headers
        $tableStyle = $hasNomorIdentitas ? ' style="table-layout:fixed;width:100%;"' : '';
        $html .= '<table class="inv-table"' . $tableStyle . '><thead>';
        if ($hasUkuran) {
            // 2-row header khusus Jalan Irigasi: baris 1 = kolom normal (rowspan 2) + "UKURAN" (colspan 3)
            $html .= '<tr>';
            foreach ($data['header'] as $ci => $h) {
                if (isset($ukuranIndices[$ci])) {
                    // Hanya render "UKURAN" sekali saat kolom pertama dari grup
                    $vals = array_values($ukuranIndices);
                    $keys = array_keys($ukuranIndices);
                    if ($ci === $keys[0]) {
                        $html .= '<th colspan="3" style="text-align:center;">U K U R A N</th>';
                    }
                    // Skip kolom ke-2 dan ke-3 dari grup (sudah di-span)
                } elseif ($kondisiIdx >= 0 && $ci === $kondisiIdx) {
                    $html .= '<th colspan="3" rowspan="1" style="padding:6px 4px;">KONDISI BARANG</th>';
                } else {
                    $html .= '<th rowspan="2">' . htmlentities(mb_strtoupper($h)) . '</th>';
                }
            }
            $html .= '</tr><tr>';
            // Baris 2: sub-kolom UKURAN
            foreach ($data['header'] as $ci => $h) {
                if (isset($ukuranIndices[$ci])) {
                    $html .= '<th>' . htmlentities(mb_strtoupper($h)) . '</th>';
                } elseif ($kondisiIdx >= 0 && $ci === $kondisiIdx) {
                    $html .= '<th style="padding:3px 2px;">Baik</th><th style="padding:3px 2px;">Rusak<br>Ringan</th><th style="padding:3px 2px;">Rusak<br>Berat</th>';
                }
                // Kolom lain sudah rowspan=2, tidak perlu dirender lagi
            }
            $html .= '</tr>';
        } elseif ($hasNomorIdentitas) {
            // Two-row header: kombinasi NOMOR IDENTITAS + KONDISI BARANG (jika ada)
            // Khusus kendaraan: width kolom diatur eksplisit
            $niEnd = $nomorIdentitasStartIdx + 3;
            // Map width per kolom berdasarkan label (khusus kendaraan)
            $niWidthMap = [
                'no'        => '3%',
                'jenis'     => '10%',
                'nama'      => '10%',
                'kode'      => '8%',
                'nup'       => '4%',
                'merk'      => '6%',
                'type'      => '6%',
                'tahun'     => '5%',
                'nomor'     => '12%',  // NOMOR IDENTITAS
                'nilai'     => '10%',  // NILAI PEROLEHAN
                'baik'      => '4%',
                'rusak'     => '5%',
                'keterangan'=> '13%',
            ];
            $html .= '<tr>';
            foreach ($data['header'] as $ci => $h) {
                if ($ci === $nomorIdentitasStartIdx) {
                    $html .= '<th rowspan="1" style="text-align:center;padding:3px 2px;width:14%;">NOMOR IDENTITAS</th>';
                } elseif ($ci > $nomorIdentitasStartIdx && $ci <= $niEnd) {
                    // Skip kolom 2-4 dari grup
                } elseif ($kondisiIdx >= 0 && $ci === $kondisiIdx) {
                    $html .= '<th colspan="3" style="padding:3px 2px;width:12%;">KONDISI BARANG</th>';
                } else {
                    $lbl = mb_strtolower($h);
                    if ($ci === 0) {
                        $w = '3%';
                    } elseif (str_contains($lbl, 'jenis') || str_contains($lbl, 'nama')) {
                        $w = '13%';
                    } elseif (str_contains($lbl, 'kode')) {
                        $w = '11%';
                    } elseif (str_contains($lbl, 'nup')) {
                        $w = '5%';
                    } elseif (str_contains($lbl, 'merk') || str_contains($lbl, 'type')) {
                        $w = '8%';
                    } elseif (str_contains($lbl, 'tahun')) {
                        $w = '6%';
                    } elseif (str_contains($lbl, 'nilai') || str_contains($lbl, 'perolehan')) {
                        $w = '12%';
                    } elseif (str_contains($lbl, 'keterangan')) {
                        $w = '16%';
                    } else {
                        $w = 'auto';
                    }
                    $html .= '<th rowspan="2" style="padding:3px 2px;width:' . $w . ';white-space:normal;">' . htmlentities(mb_strtoupper($h)) . '</th>';
                }
            }
            $html .= '</tr><tr>';
            $niSubLabel = implode('<br>', array_map(fn($l) => htmlentities(mb_strtoupper($l)) . ' :', $nomorIdentitasLabels));
            $html .= '<th style="text-align:center;padding:2px 3px;white-space:nowrap;line-height:1.4;width:14%;">' . $niSubLabel . '</th>';
            if ($kondisiIdx >= 0) {
                $html .= '<th style="padding:3px 1px;width:4%;">Baik</th><th style="padding:3px 1px;width:4%;">Rusak<br>Ringan</th><th style="padding:3px 1px;width:4%;">Rusak<br>Berat</th>';
            }
            $html .= '</tr>';
        } elseif ($kondisiIdx >= 0) {
            // Two-row header: row 1 merges normal cols; row 2 has Baik/Rusak Ringan/Rusak Berat under kondisi
            $html .= '<tr>';
            foreach ($data['header'] as $ci => $h) {
                if ($ci === $kondisiIdx) {
                    $html .= '<th colspan="3" style="padding:6px 4px;">KONDISI BARANG</th>';
                } else {
                    $nupThStyle = ($nupRowIdx >= 0 && $ci === $nupRowIdx) ? ' style="min-width:50px;"' : '';
                    $html .= '<th rowspan="2"' . $nupThStyle . '>' . htmlentities(mb_strtoupper($h)) . '</th>';
                }
            }
            $html .= '</tr><tr>';
            $html .= '<th style="padding:3px 2px;">Baik</th><th style="padding:3px 2px;">Rusak Ringan</th><th style="padding:3px 2px;">Rusak Berat</th>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            foreach ($data['header'] as $ci => $h) {
                $nupThStyle = ($nupRowIdx >= 0 && $ci === $nupRowIdx) ? ' style="min-width:50px;"' : '';
                $html .= '<th' . $nupThStyle . '>' . htmlentities(mb_strtoupper($h)) . '</th>';
            }
            $html .= '</tr>';
        }

        // Baris nomor kolom (1, 2, 3, ...)
        $html .= '<tr class="col-num">';
        $colNum = 1;
        $niEnd = $hasNomorIdentitas ? ($nomorIdentitasStartIdx + 3) : -1;
        foreach ($data['header'] as $ci => $h) {
            if ($hasUkuran && isset($ukuranIndices[$ci])) {
                $html .= '<td>' . $colNum . '</td>';
            } elseif ($hasNomorIdentitas && $ci === $nomorIdentitasStartIdx) {
                $html .= '<td>' . $colNum . '</td>';
            } elseif ($hasNomorIdentitas && $ci > $nomorIdentitasStartIdx && $ci <= $niEnd) {
                // skip — sudah digabung ke kolom pertama, tapi colNum tetap naik
            } elseif ($kondisiIdx >= 0 && $ci === $kondisiIdx) {
                $html .= '<td colspan="3">' . $colNum . '</td>';
            } else {
                $html .= '<td>' . $colNum . '</td>';
            }
            $colNum++;
        }
        $html .= '</tr>';

        $html .= '</thead><tbody>';

        foreach ($data['rows'] as $r) {
            $html .= '<tr>';
            foreach ($r as $ci => $c) {
                if ($ci === 0) {
                    $html .= '<td class="no-col">' . htmlentities((string) ($c ?? '')) . '</td>';
                } elseif ($hasNomorIdentitas && $ci === $nomorIdentitasRowIndices[0]) {
                    // Gabungkan 4 nilai NI dalam 1 cell
                    $niHtml = implode('<br>', array_map(
                        fn($idx) => htmlentities((string)($r[$idx] ?? '-')),
                        $nomorIdentitasRowIndices
                    ));
                    $html .= '<td style="text-align:left;vertical-align:middle;line-height:1.5;width:12%;">' . $niHtml . '</td>';
                } elseif ($hasNomorIdentitas && in_array($ci, array_slice($nomorIdentitasRowIndices, 1), true)) {
                    // Skip kolom 2-4 NI (tidak render td — header NI hanya 1 kolom)
                } elseif ($kondisiIdx >= 0 && $ci === $kondisiIdx) {
                    $k = strtolower(trim((string) $c));
                    $k = str_replace(' ', '_', $k);
                    $kondisiWidth = $hasNomorIdentitas ? 'width:4%;' : '';
                    $html .= '<td class="kondisi-col" style="' . $kondisiWidth . '">' . (($k === 'baik' || $k === 'b') ? 'X' : '') . '</td>';
                    $html .= '<td class="kondisi-col" style="' . $kondisiWidth . '">' . (($k === 'rusak_ringan' || $k === 'rr') ? 'X' : '') . '</td>';
                    $html .= '<td class="kondisi-col" style="' . $kondisiWidth . '">' . (($k === 'rusak_berat' || $k === 'rb') ? 'X' : '') . '</td>';
                } else {
                    $cStr = (string)($c ?? '');
                    if (in_array($ci, $currencyRowIndices, true) && $cStr !== '' && $cStr !== '-') {
                        // Format nilai mata uang — selalu 2 desimal (7.412.000,00)
                        $cleanNum = preg_replace('/[^0-9,.]/', '', $cStr);
                        if ($cleanNum !== '') {
                            if (substr_count($cleanNum, '.') > 1) {
                                $cleanNum = str_replace('.', '', $cleanNum);
                            } elseif (str_contains($cleanNum, ',')) {
                                $cleanNum = str_replace(['.', ','], ['', '.'], $cleanNum);
                            }
                        }
                        $fv = ($cleanNum !== '' && is_numeric($cleanNum)) ? (float)$cleanNum : 0.0;
                        $formatted = number_format($fv, 2, ',', '.');
                        $html .= '<td style="text-align:right;">' . htmlentities($formatted) . '</td>';
                    } elseif (in_array($ci, $m2RowIndices, true) && $cStr !== '' && $cStr !== '-') {
                        $cleanNum = preg_replace('/[^0-9,.\-]/', '', $cStr);
                        if ($cleanNum !== '') {
                            if (substr_count($cleanNum, '.') > 1) {
                                $cleanNum = str_replace('.', '', $cleanNum);
                            } elseif (str_contains($cleanNum, ',') && str_contains($cleanNum, '.')) {
                                if (strrpos($cleanNum, ',') > strrpos($cleanNum, '.')) {
                                    $cleanNum = str_replace('.', '', $cleanNum);
                                    $cleanNum = str_replace(',', '.', $cleanNum);
                                } else {
                                    $cleanNum = str_replace(',', '', $cleanNum);
                                }
                            } elseif (str_contains($cleanNum, ',')) {
                                $cleanNum = str_replace(',', '.', $cleanNum);
                            }
                        }
                        $fv = ($cleanNum !== '' && is_numeric($cleanNum)) ? (float)$cleanNum : null;
                        $formatted = $fv === null
                            ? $cStr
                            : number_format($fv, fmod($fv, 1) != 0 ? 2 : 0, ',', '.');
                        $html .= '<td style="text-align:center;">' . htmlentities($formatted) . '</td>';
                    } elseif (in_array($ci, $centerRowIndices, true)) {
                        $nupStyle = ($nupRowIdx >= 0 && $ci === $nupRowIdx) ? 'min-width:50px;' : '';
                        $html .= '<td style="text-align:center;' . $nupStyle . '">' . htmlentities((string) ($c ?? '')) . '</td>';
                    } else {
                        $html .= '<td style="text-align:left;">' . htmlentities((string) ($c ?? '')) . '</td>';
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';

        // Baris JUMLAH HARGA
        $jumlahHtml = '<tr class="jumlah-row">';
        if ($nilaiVisualIdx > 0) {
            $jumlahHtml .= '<td colspan="' . $nilaiVisualIdx . '" style="text-align:center;">JUMLAH HARGA</td>';
            $jumlahHtml .= '<td style="text-align:right;">' . ($nilaiTotal > 0 ? number_format($nilaiTotal, 2, ',', '.') : '') . '</td>';
            $remaining = $totalVisualCols - $nilaiVisualIdx - 1;
            if ($remaining > 0) {
                $jumlahHtml .= '<td colspan="' . $remaining . '"></td>';
            }
        } else {
            $jumlahHtml .= '<td colspan="' . $totalVisualCols . '" style="text-align:center;">JUMLAH HARGA</td>';
        }
        $jumlahHtml .= '</tr>';
        $html .= '<tfoot>' . $jumlahHtml . '</tfoot></table>';

        // Filter pejabat dengan nama tidak kosong
        $activePejabats = [];
        if (is_array($pejabatSnapshot)) {
            foreach ($pejabatSnapshot as $p) {
                if (!$p) continue;
                $nama = trim((string)($p['nama'] ?? ($p['nama_pejabat'] ?? '')));
                if ($nama !== '') {
                    $activePejabats[] = $p;
                }
            }
        }

        if (count($activePejabats) > 0) {
            // Footer: Tim Inventarisasi (kanan) — Petunjuk Pengisian dihapus
            $html .= '<table class="footer-table"><tr>';

            // Kolom kiri: kosong (spacer)
            $html .= '<td class="footer-petunjuk"></td>';

            // Kolom kanan: Tim Inventarisasi — format acuan: "1. ............( )"
            $html .= '<td class="footer-ttd">';
            $html .= '<div class="ttd-title">TIM INVENTARISASI</div>';
            $html .= '<table width="100%" style="border-collapse:collapse;">';

            $n = 1;
            foreach ($activePejabats as $p) {
                $nama = htmlentities(trim((string)($p['nama'] ?? ($p['nama_pejabat'] ?? ''))));
                $ttdType      = trim((string)($p['ttd_type'] ?? 'gambar'));
                $isElektronik = ($ttdType === 'elektronik');
                $imgTag       = '';

                if ($includeSignatureImage && !empty($p['ttd_path'])) {
                    $full = storage_path('app/public/' . ltrim($p['ttd_path'], '/'));
                    if (file_exists($full)) {
                        $ext     = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                        $mime    = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : 'image/png';
                        $dataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
                        $imgClass = $isElektronik ? 'ttd-img' : 'ttd-img-gambar';
                        $imgTag  = '<img class="' . $imgClass . '" src="' . $dataUri . '" alt="ttd"/>';
                    }
                }

                // Format: "N. Nama        (    [ttd di tengah]    )" — kurung lebar untuk ttd
                $html .= '<tr>';
                $html .= '<td style="font-size:11px;vertical-align:bottom;padding:0 0 18px 0;white-space:nowrap;">'
                       . $n . '. ' . $nama . '</td>';
                $html .= '<td style="vertical-align:bottom;padding:0 0 10px 8px;">'
                       . '<table width="100%" style="border-collapse:collapse;"><tr>'
                       . '<td style="font-size:12px;width:12px;vertical-align:middle;">(</td>'
                       . '<td style="text-align:center;vertical-align:middle;">' . $imgTag . '</td>'
                       . '<td style="font-size:12px;width:12px;vertical-align:middle;text-align:right;">)</td>'
                       . '</tr></table></td>';
                $html .= '</tr>';
                $n++;
            }

            $html .= '</table>';
            $html .= '</td>';

            $html .= '</tr></table>';
        };

        if ($bodyOnly) {
            // bodyOnly mode: JANGAN include footer script
            return $html;
        }
        $html .= self::buildFooterHtml('Laporan Hasil Inventaris Aset Desa Berupa ' . ($jenis->nama ?? 'Inventaris'));
        $html .= '</body></html>';
        return $html;
    }

    // ── EXPORT ALL ────────────────────────────────────────────────────────────
    public function exportAll(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $format            = strtolower($opts['format'] ?? 'excel');
        $filters           = $opts['filters'] ?? [];
        $signatureOption   = strtolower((string)($opts['signature_option'] ?? 'with'));
        $tanggalDokumen    = (string)($opts['tanggal_dokumen'] ?? '');
        $keteranganDokumen = (string)($opts['keterangan_dokumen'] ?? '');
        $pejabatOverride   = $opts['pejabat_override'] ?? null;
        $paperSize         = strtolower((string)($opts['paper_size'] ?? 'legal'));
        $userId            = $opts['user_id'] ?? null;
        $allJenis          = JenisInventaris::orderBy('id')->get();
        if ($allJenis->isEmpty()) throw new \RuntimeException('Tidak ada jenis inventaris.');
        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp = Carbon::now()->format('Ymd_His');
        if ($format === 'excel') {
            $filename     = 'semua_inventaris_' . $timestamp . '.xlsx';
            $relativePath = $baseDir . '/' . $filename;
            $includeImg   = ($signatureOption !== 'without');
            $export       = new \App\Exports\AllInventarisExport($filters, false, [], $tanggalDokumen, $keteranganDokumen, $includeImg);
            Excel::store($export, $relativePath, $this->disk);
            $log = $this->createAllJenisLog($format, $filename, $filters, $userId, $signatureOption);
            return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
        }
        if ($format === 'pdf') {
            $filename     = 'semua_inventaris_' . $timestamp . '.pdf';
            $relativePath = $baseDir . '/' . $filename;
            $includeImg   = ($signatureOption !== 'without');
            $html         = $this->renderHtmlAll($allJenis, $filters, '', $includeImg, $pejabatOverride, $paperSize);
            $this->savePdfToStorage($html, $relativePath, $paperSize);
            $log = $this->createAllJenisLog($format, $filename, $filters, $userId, $signatureOption);
            return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
        }
        throw new \InvalidArgumentException('Unsupported format: ' . $format);
    }

    // ── EXPORT BUKU INVENTARIS STANDALONE ────────────────────────────────────
    public function exportBukuInventarisStandalone(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $format            = strtolower($opts['format'] ?? 'excel');
        $filters           = $opts['filters'] ?? [];
        $pejabatOverride   = $opts['pejabat_override'] ?? null;
        $signatureOption   = strtolower((string)($opts['signature_option'] ?? 'with'));
        $tanggalDokumen    = (string)($opts['tanggal_dokumen'] ?? '');
        $keteranganDokumen = (string)($opts['keterangan_dokumen'] ?? '');
        $paperSize         = strtolower((string)($opts['paper_size'] ?? 'legal'));
        // Informasi Dokumen (Lampiran & Berita Acara)
        $docLampiran       = (string)($opts['doc_lampiran'] ?? '');
        $docBaNomor        = (string)($opts['doc_ba_nomor'] ?? '');
        $docBaTanggal      = (string)($opts['doc_ba_tanggal'] ?? '');
        $userId            = $opts['user_id'] ?? null;
        $pejabatSnapshot   = $this->resolvePejabatSnapshot($pejabatOverride, 'Buku Inventaris Aset Desa');
        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp    = Carbon::now()->format('Ymd_His');
        $filename     = 'buku_inventaris_aset_desa_' . $timestamp . '.' . $this->extForFormat($format);
        $relativePath = $baseDir . '/' . $filename;
        $includeImg   = ($signatureOption !== 'without');
        if ($format === 'excel') {
            $bukuSheet = new BukuInventarisSheet($filters, $pejabatSnapshot, $tanggalDokumen, $keteranganDokumen, $includeImg, $docLampiran, $docBaNomor, $docBaTanggal);
            Excel::store($bukuSheet, $relativePath, $this->disk);
        } elseif ($format === 'pdf') {
            $html = $this->renderBukuInventarisPdf($filters, $pejabatSnapshot, $tanggalDokumen, $keteranganDokumen, $includeImg, $docLampiran, $docBaNomor, $docBaTanggal);
            $this->savePdfToStorage($html, $relativePath, $paperSize);
        } else {
            throw new \InvalidArgumentException('Unsupported format: ' . $format);
        }
        $log = $this->createBukuInventarisSoloLog($format, $filename, $filters, $userId, $signatureOption);
        return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
    }

    // ── EXPORT JENIS INVENTARIS ───────────────────────────────────────────────
    public function exportJenisInventaris(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $jenisId         = $opts['jenis_inventaris_id'] ?? null;
        $format          = strtolower($opts['format'] ?? 'excel');
        $filters         = $opts['filters'] ?? [];
        $pejabatOverride = $opts['pejabat_override'] ?? null;
        $signatureOption = strtolower((string)($opts['signature_option'] ?? 'with'));
        $includeImg      = ($signatureOption !== 'without');
        $tanggalDokumen  = (string)($opts['tanggal_dokumen'] ?? '');
        $keteranganDokumen = (string)($opts['keterangan_dokumen'] ?? '');
        $paperSize       = strtolower((string)($opts['paper_size'] ?? 'legal'));
        // Informasi Dokumen (Lampiran & Berita Acara)
        $docLampiran     = (string)($opts['doc_lampiran'] ?? '');
        $docBaNomor      = (string)($opts['doc_ba_nomor'] ?? '');
        $docBaTanggal    = (string)($opts['doc_ba_tanggal'] ?? '');
        $userId          = $opts['user_id'] ?? null;
        $isOrphan        = ($jenisId === null);
        if (!$isOrphan && !$jenisId) throw new \InvalidArgumentException('jenis_inventaris_id required');
        if ($isOrphan) {
            $jenis = new \stdClass(); $jenis->id = null; $jenis->nama = 'Inventaris yang tidak ditemukan';
            $jenis->fields = collect([]); $jenis->pejabatDefaults = collect([]);
        } else {
            $jenis = JenisInventaris::with(['fields' => fn($q) => $q->orderBy('order','asc'), 'pejabatDefaults'])->findOrFail($jenisId);
        }
        $isVirtual = !$isOrphan && method_exists($jenis, 'isVirtual') && $jenis->isVirtual();
        $rows = $isVirtual ? $this->buildRowsAggregated($jenis, $filters) : $this->buildRows($jenis, $filters, $isOrphan);
        $pejabatSnapshot = [];
        if (is_array($pejabatOverride) && count($pejabatOverride) > 0) {
            foreach ($pejabatOverride as $p) {
                if (is_array($p) && isset($p['id'])) { $pe = Pejabat::find($p['id']); $pejabatSnapshot[] = array_merge($pe ? $pe->toArray() : [], $p); }
                elseif (is_numeric($p)) { $pe = Pejabat::find($p); $pejabatSnapshot[] = $pe ? $pe->toArray() : null; }
                else { $pejabatSnapshot[] = $p; }
            }
        } else {
            $defaults = $jenis->pejabatDefaults()->with('pejabat')->orderBy('order','asc')->get();
            foreach ($defaults as $d) { $pejabatSnapshot[] = $d->pejabat ? $d->pejabat->toArray() : null; }
        }
        $pejabatSnapshot = array_values(array_filter($pejabatSnapshot));
        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp    = Carbon::now()->format('Ymd_His');
        $slug         = Str::slug($jenis->nama ?: 'export');
        $filename     = $slug . '_' . $timestamp . '.' . $this->extForFormat($format);
        $relativePath = $baseDir . '/' . $filename;
        if ($format === 'excel') {
            $export = new InventarisExport($jenisId, $filters, $pejabatSnapshot, $includeImg, $tanggalDokumen, $docLampiran, $docBaNomor, $docBaTanggal);
            Excel::store($export, $relativePath, $this->disk);
        } elseif ($format === 'pdf') {
            $html = $this->renderHtml($jenis, $rows, $pejabatSnapshot, $includeImg, false, $tanggalDokumen, $keteranganDokumen, $filters, $paperSize, $docLampiran, $docBaNomor, $docBaTanggal);
            $this->savePdfToStorage($html, $relativePath, $paperSize);
        } else {
            throw new \InvalidArgumentException('Unsupported format: ' . $format);
        }
        $log = ExportLog::create([
            'user_id' => $userId, 'jenis_inventaris_id' => $jenisId,
            'pejabat_id' => $pejabatSnapshot[0]['id'] ?? null, 'tipe_export' => $format,
            'nama_file' => $filename, 'pejabat_override' => $pejabatSnapshot,
            'filters' => array_merge($filters, ['signature_option' => $signatureOption]),
        ]);
        return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null, 'pejabat_snapshot' => $pejabatSnapshot];
    }

    // ── EXPORT RUANGAN ────────────────────────────────────────────────────────
    public function exportRuangan(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $ruanganId         = $opts['ruangan_id'] ?? 'all';
        $format            = strtolower($opts['format'] ?? 'pdf');
        $kirFormat         = strtolower($opts['kir_format'] ?? 'standard'); // 'standard' | 'custom'
        $filters           = $opts['filters'] ?? [];
        $signatureOption   = strtolower((string)($opts['signature_option'] ?? 'with'));
        $pejabatOverride   = $opts['pejabat_override'] ?? null;
        $userId            = $opts['user_id'] ?? null;
        $tanggalDokumen    = (string)($opts['tanggal_dokumen'] ?? '');
        $keteranganDokumen = (string)($opts['keterangan_dokumen'] ?? '');
        $paperSize         = strtolower((string)($opts['paper_size'] ?? 'legal'));
        $includeImg        = ($signatureOption !== 'without');

        // Sekretaris Desa — hanya dipakai untuk Format 1
        $sekretarisUser    = \App\Models\User::with(['role','pejabat'])->whereHas('role', fn($q) => $q->where('nama','like','%sekretaris%'))->first();
        $sekretarisPejabat = $sekretarisUser?->pejabat?->toArray();

        // Resolve pejabat override berdasarkan format KIR
        // Format 1: semua override masuk sebagai Petugas Barang
        // Format 2: override[0] → Kasi Kesejahteraan, override[1] → Pengurus Barang
        $overrideResolved = [];
        if (is_array($pejabatOverride) && count($pejabatOverride) > 0) {
            foreach ($pejabatOverride as $p) {
                if (is_array($p) && isset($p['id'])) {
                    $pe = Pejabat::find($p['id']);
                    $overrideResolved[] = $pe ? $pe->toArray() : null;
                } elseif (is_numeric($p)) {
                    $pe = Pejabat::find($p);
                    $overrideResolved[] = $pe ? $pe->toArray() : null;
                } else {
                    $overrideResolved[] = $p;
                }
            }
            $overrideResolved = array_values(array_filter($overrideResolved));
        }

        if ($kirFormat === 'custom') {
            // Format 2: pisah override → posisi Kasi dan posisi Pengurus Barang
            // Jika override ada: [0] = Kasi, [1] = Pengurus Barang
            // Jika override tidak ada: gunakan default dari DB
            $kasiOverride   = $overrideResolved[0] ?? null;
            $petugasOverride = isset($overrideResolved[1]) ? $overrideResolved[1] : null;

            // Resolve penanggung jawab ruangan jika ada ruangan_id dan bukan 'all'
            $resolvedPenanggungJawab = null;
            if ($ruanganId !== 'all') {
                $ruanganObj = \App\Models\Ruangan::with('penanggungJawab.pejabat')->find($ruanganId);
                if ($ruanganObj && $ruanganObj->penanggungJawab) {
                    $pjUser = $ruanganObj->penanggungJawab;
                    if ($pjUser->pejabat) {
                        $resolvedPenanggungJawab = $pjUser->pejabat->toArray();
                    } else {
                        $resolvedPenanggungJawab = [
                            'id' => null,
                            'nama' => $pjUser->nama,
                            'jabatan' => $pjUser->role_name_display ?: 'Penanggungjawab Ruangan',
                            'ttd_path' => null,
                            'ttd_type' => 'gambar',
                        ];
                    }
                }
            }

            $kasiFormat2    = $kasiOverride   ?? $resolvedPenanggungJawab ?? $this->resolveKasiKesejahteraan();
            $petugasFormat2 = $petugasOverride ?? (Pejabat::where('is_petugas_barang', 1)->first()?->toArray());
            $petugasPejabat = $petugasFormat2 ? [$petugasFormat2] : [];
        } else {
            // Format 1: semua override = Petugas Barang
            $kasiFormat2 = null;
            if (count($overrideResolved) > 0) {
                $petugasPejabat = $overrideResolved;
            } else {
                $pb = Pejabat::where('is_petugas_barang', 1)->first();
                $petugasPejabat = $pb ? [$pb->toArray()] : [];
            }
        }

        $ruangans = $ruanganId === 'all'
            ? \App\Models\Ruangan::with('penanggungJawab.pejabat')->orderBy('kode')->get()
            : \App\Models\Ruangan::with('penanggungJawab.pejabat')->where('id', $ruanganId)->get();
        $ruanganData = []; $totalNilai = 0.0;
        foreach ($ruangans as $ruangan) {
            $query = Inventaris::where('ruangan_id', $ruangan->id)
                ->with(['jenis.fields' => fn($q) => $q->orderBy('order'), 'values.field', 'kirValues'])
                // Exclude jenis template "Inventaris yang tidak ditemukan"
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->orderBy('jenis_inventaris_id')
                ->orderBy('kode_barang');
            if (!empty($filters['tahun'])) $query->where('tahun_perolehan', '<=', $filters['tahun']);
            $inventarisList = $query->get(); $rows = []; $no = 1;
            foreach ($inventarisList as $inv) {
                $nilai = (float)($inv->nilai_perolehan ?? 0); $totalNilai += $nilai;
                $asalUsulMap = ['apbdes'=>'APBDesa','perolehan_lain'=>'Perolehan Lain yang Sah','kekayaan_asli'=>'Kekayaan Asli Desa'];
                $rows[] = [
                    'no'          => $no++,
                    'nama_barang' => $inv->display_name ?? $inv->nama_barang ?? '-',
                    'kode_barang' => $inv->kode_barang ?? '-',
                    'nup'         => $inv->nup ?? '',
                    'tahun'       => $inv->tahun_perolehan ?? '',
                    'identitas'   => $this->buildIdentitasKolom($inv),
                    'no_pol'      => $this->buildNoPol($inv),
                    'asal_usul'   => $asalUsulMap[$inv->asal_usul ?? ''] ?? ($inv->asal_usul ?? ''),
                    'harga'       => $nilai > 0 ? number_format($nilai, 2, ',', '.') : '',
                    'keterangan'  => $inv->keterangan ?? '',
                    'inv_obj'     => $inv,
                ];
            }
            $ruanganData[] = ['ruangan'=>$ruangan,'rows'=>$rows];
        }

        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp = Carbon::now()->format('Ymd_His');
        $slug = $ruanganId === 'all' ? 'kartu_inventaris_ruangan_semua' : 'kartu_inventaris_ruangan_'.$ruanganId;
        $filename = $slug.'_'.$timestamp.'.'.$this->extForFormat($format);
        $relativePath = $baseDir.'/'.$filename;

        if ($format === 'pdf') {
            if ($kirFormat === 'custom') {
                // Format 2 (Desa Demo)
                $perbekelPejabat = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%perbekel%'])->first()?->toArray();
                $html = $this->renderKartuInventarisRuanganFormat2Pdf(
                    $ruanganData, $totalNilai,
                    $perbekelPejabat, $kasiFormat2, $petugasPejabat,
                    $includeImg, $filters, $tanggalDokumen, $keteranganDokumen, $paperSize
                );
            } else {
                // Format 1 (Standar Permendagri)
                $html = $this->renderKartuInventarisRuanganPdf($ruanganData,$totalNilai,$sekretarisPejabat,$petugasPejabat,$includeImg,$filters,$tanggalDokumen,$keteranganDokumen,$paperSize);
            }
            $this->savePdfToStorage($html, $relativePath, $paperSize);
        } elseif ($format === 'excel') {
            if ($kirFormat === 'custom') {
                // Excel Format 2 (Desa Demo)
                $perbekelPejabat = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%perbekel%'])->first()?->toArray();
                $excelExport = new \App\Exports\KartuInventarisRuanganFormat2Export(
                    $ruanganData, $totalNilai,
                    $perbekelPejabat, $kasiFormat2, $petugasPejabat,
                    $includeImg, $filters, $tanggalDokumen, $keteranganDokumen
                );
            } else {
                // Excel Format 1 (Standar)
                $excelExport = new \App\Exports\KartuInventarisRuanganExport($ruanganData,$totalNilai,$sekretarisPejabat,$petugasPejabat,$includeImg,$filters,$tanggalDokumen,$keteranganDokumen);
            }
            \Maatwebsite\Excel\Facades\Excel::store($excelExport, $relativePath, $this->disk);
        } else {
            throw new \InvalidArgumentException('Unsupported format: '.$format);
        }

        // BUG 3 FIX: ExportLog — untuk Format 2 gunakan Perbekel sebagai pejabat_id, bukan Sekretaris
        if ($kirFormat === 'custom') {
            $logPejabatId = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%perbekel%'])->first()?->id ?? null;
            $logPejabatOverride = array_filter([$kasiFormat2, $petugasPejabat[0] ?? null]);
        } else {
            $logPejabatId = $sekretarisPejabat['id'] ?? null;
            $logPejabatOverride = $petugasPejabat;
        }

        $log = ExportLog::create([
            'user_id'           => $userId,
            'jenis_inventaris_id' => null,
            'pejabat_id'        => $logPejabatId,
            'tipe_export'       => $format,
            'nama_file'         => $filename,
            'pejabat_override'  => array_values($logPejabatOverride),
            'filters'           => array_merge($filters, [
                'mode'             => 'kartu_inventaris_ruangan',
                'ruangan_id'       => $ruanganId,
                'signature_option' => $signatureOption,
                'kir_format'       => $kirFormat,
            ]),
        ]);

        return ['filename'=>$filename,'path'=>$relativePath,'log_id'=>$log->id??null];
    }

    // ── HELPER METHODS ────────────────────────────────────────────────────────
    protected function buildIdentitasKolom(Inventaris $inv): string
    {
        // Konstruksi & Luas
        $konstruksiParts = [];
        $kirKonstruksi = trim((string)($inv->getKirValue('kir_konstruksi') ?? ''));
        if ($kirKonstruksi !== '') {
            $konstruksiParts[] = $kirKonstruksi;
        }
        $kirLuas = trim((string)($inv->getKirValue('kir_luas') ?? ''));
        if ($kirLuas !== '') {
            $konstruksiParts[] = $kirLuas;
        }

        if (!empty($konstruksiParts)) {
            $konstruksi = implode(' / ', $konstruksiParts);
        } else {
            $konstruksi = trim((string)($inv->getKirValue('kir_konstruksi_luas') ?? ''));
        }

        $merk = trim((string)($inv->getKirValue('kir_merk_tipe') ?? ''));

        // Warna & Judul & Pencipta
        $warnaParts = [];
        $kirWarna = trim((string)($inv->getKirValue('kir_warna') ?? ''));
        if ($kirWarna !== '') {
            $warnaParts[] = $kirWarna;
        }
        $kirJudul = trim((string)($inv->getKirValue('kir_judul') ?? ''));
        if ($kirJudul !== '') {
            $warnaParts[] = $kirJudul;
        }
        $kirPencipta = trim((string)($inv->getKirValue('kir_pencipta') ?? ''));
        if ($kirPencipta !== '') {
            $warnaParts[] = $kirPencipta;
        }

        if (!empty($warnaParts)) {
            $warna = implode(' / ', $warnaParts);
        } else {
            $warna = trim((string)($inv->getKirValue('kir_warna_judul_pencipta') ?? ''));
        }

        // Fallback to dynamic fields parsing if any of the KIR fields are empty
        if ($konstruksi === '' || $merk === '' || $warna === '') {
            if ($inv->jenis) {
                $fields = $inv->jenis->fields ?? collect();
                $dynKonstruksi = ''; $dynMerk = ''; $dynWarna = '';
                foreach ($fields as $field) {
                    $label = strtolower(trim($field->label_field ?? ''));
                    $val   = trim((string)($field->getValueFromInventaris($inv) ?? ''));
                    if ($val === '' || $val === '-') continue;
                    if (str_contains($label,'konstruksi') || str_contains($label,'luas')) { if ($dynKonstruksi==='') $dynKonstruksi=$val; }
                    elseif (str_contains($label,'merk') || str_contains($label,'merek') || str_contains($label,'type') || str_contains($label,'tipe')) { if ($dynMerk==='') $dynMerk=$val; }
                    elseif (str_contains($label,'warna') || str_contains($label,'judul') || str_contains($label,'pencipta')) { if ($dynWarna==='') $dynWarna=$val; }
                }
                if ($konstruksi === '') $konstruksi = $dynKonstruksi;
                if ($merk === '') $merk = $dynMerk;
                if ($warna === '') $warna = $dynWarna;
            }
        }

        $parts = [];
        if ($konstruksi !== '' && $konstruksi !== '-') {
            $parts[] = $konstruksi;
        }
        if ($merk !== '' && $merk !== '-') {
            $parts[] = $merk;
        }
        if ($warna !== '' && $warna !== '-') {
            $parts[] = $warna;
        }

        if (empty($parts)) {
            return '/ /';
        }

        return implode(' / ', $parts);
    }

    protected function buildNoPol(Inventaris $inv): string
    {
        $nopol = trim((string)($inv->getKirValue('kir_no_pol') ?? ''));
        if ($nopol !== '' && $nopol !== '-') {
            return $nopol;
        }

        // Fallback for older assets
        if (!$inv->jenis) return '';
        foreach ($inv->jenis->fields ?? collect() as $field) {
            $label = strtolower(trim($field->label_field ?? ''));
            if (str_contains($label,'no pol') || str_contains($label,'nopol') || str_contains($label,'nomor polisi') || str_contains($label,'no. pol')) {
                $val = trim((string)($field->getValueFromInventaris($inv) ?? ''));
                if ($val !== '' && $val !== '-') return $val;
            }
        }
        return '';
    }

    protected function savePdfToStorage(string $html, string $relativePath, string $paperSize): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $this->applyPaperSize($pdf, $paperSize);
            Storage::disk($this->disk)->put($relativePath, $pdf->output());
        } elseif (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled'=>true]);
            $dompdf->loadHtml($html);
            [$size, $orientation] = $this->resolvePaperSize($paperSize);
            $dompdf->setPaper($size, $orientation);
            $dompdf->render();
            Storage::disk($this->disk)->put($relativePath, $dompdf->output());
        } else {
            Storage::disk($this->disk)->put($relativePath, $html);
        }
    }

    protected function applyPaperSize($pdf, string $paperSize): void
    {
        [$size, $orientation] = $this->resolvePaperSize($paperSize);
        $pdf->setPaper($size, $orientation);
    }

    protected function resolvePaperSize(string $paperSize): array
    {
        return match(strtolower($paperSize)) {
            'letter' => ['letter', 'landscape'],
            'f4'     => [[0, 0, 595.28, 935.43], 'landscape'],
            default  => ['legal', 'landscape'],
        };
    }

    protected function extForFormat(string $format): string
    {
        return match(strtolower($format)) { 'excel' => 'xlsx', 'pdf' => 'pdf', default => 'bin' };
    }

    protected function resolvePejabatSnapshot(?array $override, string $jenisNama): array
    {
        if (is_array($override) && count($override) > 0) {
            $result = [];
            foreach ($override as $p) {
                if (is_array($p) && isset($p['id'])) { $pe = Pejabat::find($p['id']); $result[] = array_merge($pe ? $pe->toArray() : [], $p); }
                elseif (is_numeric($p)) { $pe = Pejabat::find($p); $result[] = $pe ? $pe->toArray() : null; }
                else { $result[] = $p; }
            }
            return array_values(array_filter($result));
        }
        $jenis = JenisInventaris::where('nama', $jenisNama)->first();
        if (!$jenis) return [];
        return $jenis->pejabatDefaults()->with('pejabat')->orderBy('order','asc')->get()->map(fn($d) => $d->pejabat?->toArray())->filter()->values()->toArray();
    }

    protected function createAllJenisLog(string $format, string $filename, array $filters, ?int $userId, string $signatureOption): ExportLog
    {
        return ExportLog::create(['user_id'=>$userId,'jenis_inventaris_id'=>null,'tipe_export'=>$format,'nama_file'=>$filename,'pejabat_override'=>[],'filters'=>array_merge($filters,['mode'=>'all_jenis','signature_option'=>$signatureOption])]);
    }

    protected function createBukuInventarisSoloLog(string $format, string $filename, array $filters, ?int $userId, string $signatureOption): ExportLog
    {
        return ExportLog::create(['user_id'=>$userId,'jenis_inventaris_id'=>null,'tipe_export'=>$format,'nama_file'=>$filename,'pejabat_override'=>[],'filters'=>array_merge($filters,['mode'=>'buku_inventaris_standalone','signature_option'=>$signatureOption])]);
    }

    protected function renderBukuInventarisPdf(array $filters, array $pejabatSnapshot, string $tanggalDokumen, string $keteranganDokumen, bool $includeImg, string $docLampiran = '', string $docBaNomor = '', string $docBaTanggal = '', bool $bodyOnly = false): string
    {
        return '<!-- Buku Inventaris PDF placeholder -->';
    }

    protected function renderKartuInventarisRuanganPdf(array $ruanganData, float $totalNilai, ?array $sekretarisPejabat, array $petugasPejabat, bool $includeImg, array $filters = [], string $tanggalDokumen = '', string $keteranganDokumen = '', string $paperSize = 'legal'): string
    {
        $desa = config('desa');
        $pageSizeCss = match(strtolower($paperSize)) { 'letter'=>'letter landscape','f4'=>'21cm 33cm landscape',default=>'legal landscape' };
        $css = '@page{size:'.$pageSizeCss.';margin:10mm 8mm 22mm 8mm;}body{font-family:Arial,sans-serif;font-size:9px;color:#000;margin:0;}h1.doc-title{text-align:center;font-size:13px;font-weight:bold;margin:0 0 45px 0;}.info-table{width:50%;border-collapse:collapse;margin-top:14px;margin-bottom:14px;font-size:9px;}.info-table td{padding:2px 4px;border:none;vertical-align:top;font-weight:bold;}.info-table td:first-child{width:95px;}.info-table td:nth-child(2){width:10px;text-align:center;}.inv-table{width:100%;border-collapse:collapse;font-size:8px;}.inv-table th{background:#fff;font-weight:bold;text-align:center;border:1px solid #000;padding:3px 2px;vertical-align:middle;font-size:8px;}.inv-table td{border:1px solid #000;padding:2px 3px;vertical-align:top;}.ruangan-row td{background:#d9d9d9;font-style:italic;font-weight:bold;font-size:8.5px;padding:3px 4px;border:1px solid #000;}.jumlah-row td{font-weight:bold;border:1px solid #000;padding:2px 3px;}.footer-section{margin-top:30px;page-break-inside:avoid;}.ttd-table{width:100%;border-collapse:collapse;}.ttd-table td{border:none;vertical-align:top;padding:0 8px;text-align:center;font-size:9px;}.ttd-space{height:55px;}.ttd-name{text-decoration:underline;font-weight:bold;}' . self::buildFooterCss();

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Kartu Inventaris Ruangan</title><style>'.$css.'</style></head><body>';
        $html .= '<h1 class="doc-title">KARTU INVENTARIS RUANGAN</h1>';
        $html .= '<table class="info-table"><tr><td>Provinsi</td><td>:</td><td>'.htmlspecialchars(($desa['provinsi_kode']??'').'. '.($desa['provinsi_nama']??'')).'</td></tr><tr><td>Kab./Kota</td><td>:</td><td>'.htmlspecialchars(($desa['kabupaten_kode']??'').'. '.($desa['kabupaten_nama']??'')).'</td></tr><tr><td>Kecamatan</td><td>:</td><td>'.htmlspecialchars(($desa['kecamatan_kode']??'').'. '.($desa['kecamatan_nama']??'')).'</td></tr><tr><td>Desa</td><td>:</td><td>'.htmlspecialchars(($desa['kode_lokasi']??'').'. '.($desa['desa_nama']??'')).'</td></tr><tr><td>UPB</td><td>:</td><td>'.htmlspecialchars($desa['upb']??'').'</td></tr><tr><td>No. Kode Lokasi</td><td>:</td><td>'.htmlspecialchars(($desa['kode_lokasi']??'').'.').'</td></tr></table>';
        $html .= $this->renderKirTableHeader();
        foreach ($ruanganData as $rd) {
            $ruangan = $rd['ruangan']; $rows = $rd['rows'];
            if (empty($rows)) continue;
            $html .= '<tr class="ruangan-row"><td colspan="10">Nama Ruangan : '.htmlspecialchars($ruangan->kode.' .'.$ruangan->nama).'</td></tr>';
            foreach ($rows as $row) {
                $html .= '<tr><td style="text-align:center;">'.$row['no'].'</td><td>'.htmlspecialchars($row['nama_barang']).'</td><td style="text-align:center;">'.htmlspecialchars($row['kode_barang']).'</td><td style="text-align:center;">'.htmlspecialchars($row['nup']).'</td><td style="text-align:center;">'.htmlspecialchars($row['tahun']).'</td><td style="text-align:center;">'.htmlspecialchars($row['identitas']).'</td><td style="text-align:center;">'.htmlspecialchars($row['no_pol']).'</td><td>'.htmlspecialchars($row['asal_usul']).'</td><td style="text-align:right;">'.htmlspecialchars($row['harga']).'</td><td>'.htmlspecialchars($row['keterangan']).'</td></tr>';
            }
        }
        $html .= '<tr class="jumlah-row"><td colspan="8" style="text-align:center;font-weight:bold;">JUMLAH HARGA</td><td style="text-align:right;font-weight:bold;">'.number_format($totalNilai,2,',','.').'</td><td></td></tr>';
        $html .= '</tbody></table>';
        $html .= $this->renderKirTtd($sekretarisPejabat, $petugasPejabat, $includeImg, $filters, $tanggalDokumen, $keteranganDokumen);
        $html .= self::buildFooterHtml('Kartu Inventaris Ruangan');
        $html .= '</body></html>';
        return $html;
    }

    protected function renderKirTableHeader(): string
    {
        $html  = '<table class="inv-table"><thead>';
        $html .= '<tr><th rowspan="2" style="width:3%">NO</th><th rowspan="2" style="width:14%">JENIS BARANG /<br>NAMA BARANG</th><th colspan="2">N O M O R</th><th rowspan="2" style="width:5%">TAHUN<br>PEROLEHAN</th><th colspan="2">I D E N T I T A S</th><th rowspan="2" style="width:8%">ASAL-USUL<br>BARANG</th><th rowspan="2" style="width:9%">HARGA<br>(Ribuan) Rp</th><th rowspan="2" style="width:8%">KETERANGAN</th></tr>';
        $html .= '<tr><th style="width:12%">KODE BARANG</th><th style="width:6%">REG</th><th style="width:22%">KONSTRUKSI/LUAS/MERK/TYPE/WARNA/JUDUL/PENCIPTA</th><th style="width:6%">NO. POL</th></tr>';
        $html .= '<tr><td style="text-align:center;font-weight:bold;">1</td><td style="text-align:center;font-weight:bold;">2</td><td style="text-align:center;font-weight:bold;">3</td><td style="text-align:center;font-weight:bold;">4</td><td style="text-align:center;font-weight:bold;">5</td><td style="text-align:center;font-weight:bold;" colspan="2">6</td><td style="text-align:center;font-weight:bold;">7</td><td style="text-align:center;font-weight:bold;">8</td><td style="text-align:center;font-weight:bold;">9</td></tr>';
        $html .= '</thead><tbody>';
        return $html;
    }

    /**
     * Header tabel Format 2 (Desa Demo) — 13 kolom sesuai dokumen fisik desa.
     * Kolom: NO | JENIS BARANG/NAMA BARANG | MERK/MODEL | NO.SERI PABRIK |
     *        UKURAN(PxLxT) | BAHAN | THN.PEMBUATAN/PEMBELIAN | NO.KODE BARANG |
     *        JUMLAH | KEADAAN BARANG (B|KB|RB) | KET.
     */
    protected function renderKirFormat2TableHeader(): string
    {
        $html  = '<table class="inv-table"><thead>';
        $html .= '<tr>';
        $html .= '<th rowspan="2" style="width:3%">NO</th>';
        $html .= '<th rowspan="2" style="width:14%">JENIS BARANG/<br>NAMA BARANG</th>';
        $html .= '<th rowspan="2" style="width:10%">MERK/<br>MODEL</th>';
        $html .= '<th rowspan="2" style="width:8%">NO. SERI<br>PABRIK</th>';
        $html .= '<th rowspan="2" style="width:12%">UKURAN<br>(P x L x T)</th>';
        $html .= '<th rowspan="2" style="width:7%">BAHAN</th>';
        $html .= '<th rowspan="2" style="width:7%">THN.<br>PEMBUATAN/<br>PEMBELIAN</th>';
        $html .= '<th rowspan="2" style="width:12%">NO. KODE<br>BARANG</th>';
        $html .= '<th rowspan="2" style="width:4%">JML</th>';
        $html .= '<th colspan="3" style="width:13%">KEADAAN BARANG</th>';
        $html .= '<th rowspan="2" style="width:9%">KET.</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th style="width:4%">B</th>';
        $html .= '<th style="width:4%">KB</th>';
        $html .= '<th style="width:5%">RB</th>';
        $html .= '</tr>';
        // Baris nomor kolom
        $html .= '<tr>';
        for ($i = 1; $i <= 13; $i++) {
            $html .= '<td style="text-align:center;font-weight:bold;">' . $i . '</td>';
        }
        $html .= '</tr>';
        $html .= '</thead><tbody>';
        return $html;
    }

    /**
     * Resolusi Kasi Kesejahteraan untuk TTD tengah Format 2.
     * Urutan: 1) Pejabat dengan jabatan like 'Kasi Kesejahteraan'
     *         2) User dengan role like 'kasi' yang punya pejabat terkait
     *         3) null jika tidak ditemukan
     */
    protected function resolveKasiKesejahteraan(): ?array
    {
        // 1. Cari di tabel pejabats
        $pejabat = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%kasi kesejahteraan%'])
            ->first();
        if ($pejabat) return $pejabat->toArray();

        // 2. Cari user dengan role kasi yang punya pejabat
        $user = \App\Models\User::with('pejabat')
            ->whereHas('role', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kasi%']))
            ->whereHas('bidang', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kesejahteraan%']))
            ->first();
        if ($user?->pejabat) return $user->pejabat->toArray();

        // 3. Fallback: cari user role kasi yang pejabatnya juga mengandung 'kasi' di jabatan
        $user2 = \App\Models\User::with('pejabat')
            ->whereHas('role', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kasi%']))
            ->whereHas('pejabat', fn($q) => $q->whereRaw("LOWER(jabatan) LIKE ?", ['%kasi%']))
            ->first();
        if ($user2?->pejabat) return $user2->pejabat->toArray();

        return null;
    }

    /**
     * TTD Format 2 — 3 pejabat: Perbekel (kiri) | Kasi Kesejahteraan (tengah) | Pengurus Barang (kanan)
     */
    protected function renderKirFormat2Ttd(?array $perbekelPejabat, ?array $kasiKesejahteraan, array $petugasPejabat, bool $includeImg, array $filters = [], string $tanggalDokumen = '', string $keteranganDokumen = ''): string
    {
        $namaLokasi = $keteranganDokumen ?: config('desa.nama_lokasi', '');
        $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        if (!empty($tanggalDokumen)) {
            try { $dt = \Carbon\Carbon::parse($tanggalDokumen); $tglStr = $dt->day.' '.$bulan[$dt->month].' '.$dt->year; }
            catch(\Throwable $e) { $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y'); }
        } else {
            $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y');
        }
        $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi.', ' : '').$tglStr;

        $html = '<div class="footer-section"><table class="ttd-table"><tr>';

        // Helper render satu kolom TTD — return empty string jika pejabat kosong (tidak ditampilkan)
        $renderCol = function(?array $pejabatData, string $headerHtml, bool $showDate = false) use ($includeImg, $tanggalDisplay): string {
            if (!$pejabatData) return ''; // ← skip kolom jika data pejabat kosong
            $col = '<td style="text-align:center;">';
            if ($showDate) {
                $col .= '<div style="font-size:9px;margin-bottom:4px;">'.htmlentities($tanggalDisplay).'</div>';
            }
            $col .= '<div style="font-weight:bold;margin-bottom:4px;">'.$headerHtml.'</div>';
            if ($includeImg && !empty($pejabatData['ttd_path'])) {
                $full = storage_path('app/public/'.ltrim($pejabatData['ttd_path'], '/'));
                if (file_exists($full)) {
                    $mime = mime_content_type($full) ?: 'image/png';
                    $col .= '<img src="data:'.$mime.';base64,'.base64_encode(file_get_contents($full)).'" style="max-height:50px;max-width:120px;display:block;margin:0 auto;">';
                } else { $col .= '<div class="ttd-space"></div>'; }
            } else { $col .= '<div class="ttd-space"></div>'; }
            $col .= '<div class="ttd-name">'.htmlentities($pejabatData['nama'] ?? '').'</div>';
            $col .= '</td>';
            return $col;
        };

        // Kiri: Perbekel Mengwi
        $html .= $renderCol(
            $perbekelPejabat,
            'Mengetahui<br>Perbekel Mengwi,'
        );

        // Tengah: posisi Kasi — label jabatan dari data pejabat (bukan hardcode)
        $kasiJabatanLabel = !empty($kasiKesejahteraan['jabatan'])
            ? htmlentities($kasiKesejahteraan['jabatan'])
            : 'Penanggung Jawab';
        $html .= $renderCol(
            $kasiKesejahteraan,
            'Penanggungjawab,<br>'.$kasiJabatanLabel.','
        );

        // Kanan: posisi Pengurus Barang — label jabatan dari data pejabat (bukan hardcode)
        $petugasData         = $petugasPejabat[0] ?? null;
        $petugasJabatanLabel = !empty($petugasData['jabatan'])
            ? htmlentities($petugasData['jabatan'])
            : 'Pengurus Barang Desa Demo';
        $html .= $renderCol(
            $petugasData,
            $petugasJabatanLabel.',',
            true
        );

        $html .= '</tr></table></div>';
        return $html;
    }

    /**
     * Generate PDF Format 2 (Desa Demo) untuk Kartu Inventaris Ruangan.
     */
    protected function renderKartuInventarisRuanganFormat2Pdf(array $ruanganData, float $totalNilai, ?array $perbekelPejabat, ?array $kasiKesejahteraan, array $petugasPejabat, bool $includeImg, array $filters = [], string $tanggalDokumen = '', string $keteranganDokumen = '', string $paperSize = 'legal'): string
    {
        $desa = config('desa');
        $tahunFilter = (int)($filters['tahun'] ?? date('Y'));
        if (!$tahunFilter) $tahunFilter = (int)date('Y');

        $pageSizeCss = match(strtolower($paperSize)) { 'letter'=>'letter landscape','f4'=>'21cm 33cm landscape',default=>'legal landscape' };
        $css = '@page{size:'.$pageSizeCss.';margin:10mm 8mm 22mm 8mm;}body{font-family:Arial,sans-serif;font-size:9px;color:#000;margin:0;}h1.doc-title{text-align:center;font-size:13px;font-weight:bold;margin:0 0 45px 0;}.info-table{width:60%;border-collapse:collapse;margin-top:14px;margin-bottom:14px;font-size:9px;}.info-table td{padding:1px 3px;border:none;vertical-align:top;font-weight:bold;}.info-table td:first-child{width:90px;}.info-table td:nth-child(2){width:10px;text-align:center;}.info-kode{font-size:9px;font-weight:bold;text-align:right;margin-bottom:6px;}.inv-table{width:100%;border-collapse:collapse;font-size:7.5px;}.inv-table th{background:#fff;font-weight:bold;text-align:center;border:1px solid #000;padding:2px 1px;vertical-align:middle;font-size:7px;}.inv-table td{border:1px solid #000;padding:2px 2px;vertical-align:top;}.ruangan-row td{background:#d9d9d9;font-style:italic;font-weight:bold;font-size:8px;padding:2px 3px;border:1px solid #000;}.jumlah-row td{font-weight:bold;border:1px solid #000;padding:2px 3px;}.footer-section{margin-top:30px;page-break-inside:avoid;}.ttd-table{width:100%;border-collapse:collapse;}.ttd-table td{border:none;vertical-align:top;padding:0 6px;text-align:center;font-size:9px;}.ttd-space{height:55px;}.ttd-name{text-decoration:underline;font-weight:bold;}' . self::buildFooterCss();

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Kartu Inventaris Ruangan - Format Desa</title><style>'.$css.'</style></head><body>';

        $totalRooms = count($ruanganData);
        $idx = 0;
        foreach ($ruanganData as $rd) {
            $ruangan = $rd['ruangan'];
            $rows    = $rd['rows'];
            if (empty($rows)) continue;

            $idx++;

            // Resolve penanggung jawab for this specific room
            $resolvedPj = null;
            if ($ruangan->penanggungJawab) {
                $pjUser = $ruangan->penanggungJawab;
                if ($pjUser->pejabat) {
                    $resolvedPj = $pjUser->pejabat->toArray();
                } else {
                    $resolvedPj = [
                        'id' => null,
                        'nama' => $pjUser->nama,
                        'jabatan' => $pjUser->role_name_display ?: 'Penanggungjawab Ruangan',
                        'ttd_path' => null,
                        'ttd_type' => 'gambar',
                    ];
                }
            }
            $roomSigner = $resolvedPj ?? $kasiKesejahteraan; // fall back to Kasi Kesejahteraan if no penanggung jawab

            // Start of a room's KIR document/page
            $html .= '<div class="kir-room-page">';
            $html .= '<h1 class="doc-title">KARTU INVENTARIS RUANGAN TAHUN '.$tahunFilter.'</h1>';

            // Baris 1: PROVINSI, KABUPATEN, SATUAN KERJA
            $html .= '<table style="width:100%;border-collapse:collapse;margin-bottom:0;font-size:9px;font-weight:bold;">';
            $html .= '<tr>';
            $html .= '<td style="width:65%;vertical-align:top;padding:0;">';
            $html .= '<table style="border-collapse:collapse;font-size:9px;">';
            $html .= '<tr><td style="width:90px;padding:1px 3px;border:none;">PROVINSI</td><td style="width:10px;padding:1px 3px;border:none;text-align:center;">:</td><td style="padding:1px 3px;border:none;font-weight:bold;">'.htmlspecialchars($desa['provinsi_nama'] ?? '').'</td></tr>';
            $html .= '<tr><td style="padding:1px 3px;border:none;">KABUPATEN</td><td style="padding:1px 3px;border:none;text-align:center;">:</td><td style="padding:1px 3px;border:none;font-weight:bold;">'.htmlspecialchars($desa['kabupaten_nama'] ?? '').'</td></tr>';
            $html .= '<tr><td style="padding:1px 3px;border:none;">SATUAN KERJA</td><td style="padding:1px 3px;border:none;text-align:center;">:</td><td style="padding:1px 3px;border:none;font-weight:bold;">'.htmlspecialchars($desa['upb'] ?? '').'</td></tr>';
            $html .= '</table>';
            $html .= '</td>';
            $html .= '<td style="vertical-align:top;text-align:right;padding:1px 3px;border:none;">&nbsp;</td>';
            $html .= '</tr>';

            // Baris 2: RUANGAN (kiri) sejajar horizontal dengan NO. KODE LOKASI (kanan)
            $html .= '<tr>';
            $html .= '<td style="vertical-align:top;padding:0;">';
            $html .= '<table style="border-collapse:collapse;font-size:9px;">';
            $html .= '<tr><td style="width:90px;padding:1px 3px;border:none;">RUANGAN</td><td style="width:10px;padding:1px 3px;border:none;text-align:center;">:</td><td style="padding:1px 3px;border:none;font-weight:bold;">'.htmlspecialchars(strtoupper($ruangan->nama)).'</td></tr>';
            $html .= '</table>';
            $html .= '</td>';
            $html .= '<td style="vertical-align:middle;text-align:right;font-size:9px;font-weight:bold;padding:1px 3px;border:none;white-space:nowrap;">NO. KODE LOKASI : '.htmlspecialchars($desa['kode_lokasi'] ?? '').'</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '<div style="margin-bottom:4px;"></div>';

            $html .= $this->renderKirFormat2TableHeader();

            $no = 1;
            foreach ($rows as $rowData) {
                $inv = isset($rowData['inv_obj']) ? $rowData['inv_obj'] : null;

                $merkModel   = $inv ? trim((string)($inv->getKirValue('kir_merk_tipe') ?? '')) : '';
                $noSeri      = $inv ? trim((string)($inv->getKirValue('kir_no_seri_pabrik') ?? '')) : '';
                $panjang     = $inv ? trim((string)($inv->getKirValue('kir_panjang') ?? '')) : '';
                $lebar       = $inv ? trim((string)($inv->getKirValue('kir_lebar') ?? '')) : '';
                $tinggi      = $inv ? trim((string)($inv->getKirValue('kir_tinggi') ?? '')) : '';
                $bahan       = $inv ? trim((string)($inv->getKirValue('kir_bahan') ?? '')) : '';
                $kirKondisi  = $inv ? trim((string)($inv->getKirValue('kir_kondisi') ?? ($inv->kondisi ?? ''))) : '';

                $ukuranParts = array_filter([$panjang, $lebar, $tinggi]);
                $ukuran = implode(' x ', $ukuranParts);

                $kondisiNorm = strtolower(str_replace(' ', '_', trim($kirKondisi)));
                $isB  = in_array($kondisiNorm, ['baik', 'b']) ? 'B' : '';
                $isKB = in_array($kondisiNorm, ['rusak_ringan', 'kb', 'kurang_baik']) ? 'KB' : '';
                $isRB = in_array($kondisiNorm, ['rusak_berat', 'rb']) ? 'RB' : '';

                $html .= '<tr>';
                $html .= '<td style="text-align:center;">'.$no.'</td>';
                $html .= '<td>'.htmlspecialchars($rowData['nama_barang']).'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($merkModel).'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($noSeri ?: '-').'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($ukuran ?: '-').'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($bahan ?: '-').'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($rowData['tahun'] ?? '-').'</td>';
                $html .= '<td style="text-align:center;">'.htmlspecialchars($rowData['kode_barang']).'</td>';
                $html .= '<td style="text-align:center;">1</td>';
                $html .= '<td style="text-align:center;">'.$isB.'</td>';
                $html .= '<td style="text-align:center;">'.$isKB.'</td>';
                $html .= '<td style="text-align:center;">'.$isRB.'</td>';
                $html .= '<td>'.htmlspecialchars($rowData['keterangan'] ?? '').'</td>';
                $html .= '</tr>';
                $no++;
            }

            $html .= '</tbody></table>';
            $html .= $this->renderKirFormat2Ttd($perbekelPejabat, $roomSigner, $petugasPejabat, $includeImg, $filters, $tanggalDokumen, $keteranganDokumen);
            $html .= self::buildFooterHtml('Kartu Inventaris Ruangan - Format Desa Demo');
            $html .= '</div>';

            if ($idx < $totalRooms) {
                $html .= '<div style="page-break-after:always;"></div>';
            }
        }

        $html .= '</body></html>';
        return $html;
    }

    protected function renderKirTtd(?array $sekretarisPejabat, array $petugasPejabat, bool $includeImg, array $filters = [], string $tanggalDokumen = '', string $keteranganDokumen = ''): string
    {
        $namaLokasi = $keteranganDokumen ?: config('desa.nama_lokasi','');
        $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        if (!empty($tanggalDokumen)) { try { $dt = \Carbon\Carbon::parse($tanggalDokumen); $tglStr = $dt->day.' '.$bulan[$dt->month].' '.$dt->year; } catch(\Throwable $e) { $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y'); } }
        else { $tglStr = date('d').' '.$bulan[(int)date('n')].' '.date('Y'); }
        $tanggalDisplay = ($namaLokasi!==''?$namaLokasi.', ':'').$tglStr;
        $html = '<div class="footer-section"><table class="ttd-table"><tr>';

        // Kolom kiri: Sekretaris Desa — hanya tampil jika ada datanya
        if ($sekretarisPejabat) {
            $html .= '<td style="width:50%;text-align:center;"><div style="font-weight:bold;margin-bottom:4px;">MENGETAHUI<br>SEKRETARIS DESA<br><small>Selaku Pembantu Pengelola Barang Milik Desa</small></div>';
            if ($includeImg && !empty($sekretarisPejabat['ttd_path'])) { $full = storage_path('app/public/'.ltrim($sekretarisPejabat['ttd_path'],'/')); if (file_exists($full)) { $mime = mime_content_type($full)?:'image/png'; $html .= '<img src="data:'.$mime.';base64,'.base64_encode(file_get_contents($full)).'" style="max-height:50px;max-width:120px;display:block;margin:0 auto;">'; } else { $html .= '<div class="ttd-space"></div>'; } } else { $html .= '<div class="ttd-space"></div>'; }
            $html .= '<div class="ttd-name">'.htmlentities($sekretarisPejabat['nama']??'').'</div>';
            $html .= '</td>';
        }

        // Kolom kanan: Petugas Barang — hanya tampil jika ada datanya
        $petugasItem = $petugasPejabat[0] ?? null;
        if ($petugasItem) {
            $html .= '<td style="width:50%;text-align:center;"><div style="font-size:9px;margin-bottom:4px;">'.htmlentities($tanggalDisplay).'</div><div style="font-weight:bold;margin-bottom:4px;">PETUGAS / PENGURUS BARANG MILIK DESA</div>';
            if ($includeImg && !empty($petugasItem['ttd_path'])) { $full = storage_path('app/public/'.ltrim($petugasItem['ttd_path'],'/')); if (file_exists($full)) { $mime = mime_content_type($full)?:'image/png'; $html .= '<img src="data:'.$mime.';base64,'.base64_encode(file_get_contents($full)).'" style="max-height:50px;max-width:120px;display:block;margin:0 auto;">'; } else { $html .= '<div class="ttd-space"></div>'; } } else { $html .= '<div class="ttd-space"></div>'; }
            $html .= '<div class="ttd-name">'.htmlentities($petugasItem['nama']??'').'</div>';
            $html .= '</td>';
        }

        $html .= '</tr></table></div>';
        return $html;
    }
}
