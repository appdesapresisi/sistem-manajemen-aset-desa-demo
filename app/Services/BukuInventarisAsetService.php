<?php

namespace App\Services;

use App\Models\ExportLog;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BukuInventarisAsetService
{
    protected string $disk = 'local';

    // Export Buku Inventaris Aset ke Excel atau PDF
    public function export(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $tahun   = (int) ($opts['tahun'] ?? date('Y'));
        $periode = $this->normalizePeriode($opts['periode'] ?? 'tahunan');
        $format  = strtolower($opts['format'] ?? 'excel');
        $paper   = strtolower($opts['paper_size'] ?? 'a4');
        $sigOpt  = strtolower($opts['signature_option'] ?? 'with');
        $tanggal = (string) ($opts['tanggal_dokumen'] ?? '');
        $userId  = $opts['user_id'] ?? \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id;
        $inclImg = ($sigOpt !== 'without');

        $slug         = 'buku_inventaris_aset_' . $periode . '_' . $tahun;
        $baseDir      = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp    = Carbon::now()->format('Ymd_His');
        $ext          = ($format === 'pdf') ? 'pdf' : 'xlsx';
        $filename     = $slug . '_' . $timestamp . '.' . $ext;
        $relativePath = $baseDir . '/' . $filename;

        $rows = $this->buildRows($tahun, $periode);

        if ($format === 'excel') {
            $export = new \App\Exports\BukuInventarisAsetExport(
                $rows,
                $tahun,
                $periode,
                $tanggal,
                $inclImg
            );
            Excel::store($export, $relativePath, $this->disk);
        } else {
            $html = $this->renderPdf($rows, $tahun, $periode, $tanggal, $inclImg, $paper);
            $this->savePdf($html, $relativePath, $paper);
        }

        $log = ExportLog::create([
            'user_id'          => $userId,
            'tipe_export'      => $format,
            'nama_file'        => $filename,
            'pejabat_override' => [],
            'filters'          => [
                'mode'    => 'buku_inventaris_aset',
                'tahun'   => $tahun,
                'periode' => $periode,
            ],
        ]);

        return [
            'filename' => $filename,
            'path'     => $relativePath,
            'log_id'   => $log->id ?? null,
        ];
    }

    // Bangun baris data untuk seluruh aset sesuai filter periode
    public function buildRows(int $tahun, string $periode): array
    {
        $periode = $this->normalizePeriode($periode);

        $query = Inventaris::with(['jenis', 'kibValues', 'laporanAsetValue'])
            ->whereNotNull('kode_barang')
            ->whereNotNull('nama_barang')
            ->whereHas('jenis', fn($q) => $q->where('nama', '!=', JenisInventaris::NAMA_TIDAK_DITEMUKAN))
            ->orderBy('kode_barang')
            ->orderBy('tahun_perolehan')
            ->orderBy('nama_barang')
            ->orderByRaw('CAST(nup AS UNSIGNED) ASC')
            ->orderBy('id');

        // Filter periode
        if ($periode === 'tahunan') {
            // Saldo akhir tahun: semua aset dengan tahun_perolehan <= $tahun
            $query->where('tahun_perolehan', '<=', $tahun);

        } elseif ($periode === 'semester_1') {
            // Saldo per 30 Juni: tahun < $tahun ATAU (tahun = $tahun AND bulan <= 6 atau null)
            $query->where(function ($q) use ($tahun) {
                $q->where('tahun_perolehan', '<', $tahun)
                  ->orWhere(function ($sq) use ($tahun) {
                      $sq->where('tahun_perolehan', $tahun)
                         ->where(function ($inner) {
                             $inner->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '<=', 6))
                                   ->orWhereDoesntHave('laporanAsetValue');
                         });
                  });
            });

        } elseif ($periode === 'semester_2') {
            // Saldo per 31 Des: semua semester_1 PLUS bulan 7-12 di tahun yang sama
            $query->where(function ($q) use ($tahun) {
                // Tahun sebelum periode
                $q->where('tahun_perolehan', '<', $tahun)
                  // Tahun sama, semester 1 (bulan <= 6 atau null)
                  ->orWhere(function ($sq) use ($tahun) {
                      $sq->where('tahun_perolehan', $tahun)
                         ->where(function ($inner) {
                             $inner->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '<=', 6))
                                   ->orWhereDoesntHave('laporanAsetValue');
                         });
                  })
                  // Tahun sama, semester 2 (bulan 7-12)
                  ->orWhere(function ($sq) use ($tahun) {
                      $sq->where('tahun_perolehan', $tahun)
                         ->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '>=', 7));
                  });
            });
        }

        $data = $query->get();

        $rows = [];
        $no   = 1;

        foreach ($data as $inv) {
            $nilai = (float) ($inv->nilai_perolehan ?? 0);

            // Ukuran M2 dari KIB values
            $ukuran = '';
            $kibType = $inv->jenis?->kib_type ?? null;
            if ($kibType === 'A' || $kibType === 'C' || $kibType === 'D') {
                $rawLuas = $inv->getKibValue('luas_m2');
                if ($rawLuas !== null && $rawLuas !== '') {
                    $ukuran = $rawLuas;
                }
            }

            $rows[] = [
                'no'                => $no++,
                'kode'              => $inv->kode_barang ?? '-',
                'nama_barang'       => $inv->nama_barang ?? ($inv->display_name ?? '-'),
                'nup'               => $inv->nup ?? '',
                'merk_type'         => $inv->getKibValue('merk_type') ?? '',
                'bukti_kepemilikan' => $this->resolveBuktiKepemilikan($inv),
                'tahun_perolehan'   => $inv->tahun_perolehan ?? '',
                'ukuran_m2'         => $ukuran,
                'nilai'             => $nilai,
                'keterangan'        => $inv->keterangan ?? '',
            ];
        }

        return $rows;
    }

    // Resolve teks bukti kepemilikan berdasarkan kolom bukti_kepemilikan atau kib_type
    public function resolveBuktiKepemilikan(Inventaris $inv): string
    {
        if (!empty($inv->bukti_kepemilikan)) {
            return $inv->bukti_kepemilikan;
        }

        $kibType = $inv->jenis?->kib_type ?? null;

        switch ($kibType) {
            case 'A':
                // Tanah → Sertifikat
                $noSertifikat = trim((string) ($inv->getKibValue('sertifikat_nomor') ?? ''));
                if ($noSertifikat !== '') {
                    return 'Sertifikat';
                }
                return '-';

            case 'B':
                // Peralatan & Mesin / Kendaraan → STNK & BPKB
                $noPolisi = trim((string) ($inv->getKibValue('no_polisi') ?? ''));
                $noBpkb   = trim((string) ($inv->getKibValue('no_bpkb') ?? ''));
                if ($noPolisi !== '' && $noBpkb !== '') {
                    return 'STNK & BPKB';
                }
                if ($noBpkb !== '') {
                    return 'BPKB';
                }
                if ($noPolisi !== '') {
                    return 'STNK';
                }
                return '-';

            case 'C':
                // Gedung dan Bangunan → Dokumen Gedung (pengganti IMB)
                $dokNo = trim((string) ($inv->getKibValue('dokumen_nomor') ?? ''));
                if ($dokNo !== '') {
                    return 'Dok. Gedung';
                }
                return '-';

            case 'D':
                // Jalan, Irigasi, Jaringan → Dokumen Jaringan
                $dokNo = trim((string) ($inv->getKibValue('dokumen_nomor') ?? ''));
                if ($dokNo !== '') {
                    return 'Dok. Jaringan';
                }
                return '-';

            case 'E':
                // Aset Tetap Lainnya → biasanya tidak ada bukti kepemilikan fisik
                return '-';

            case 'F':
                // Konstruksi Dalam Pengerjaan → Dokumen Kontrak
                $dokNo = trim((string) ($inv->getKibValue('dokumen_nomor') ?? ''));
                if ($dokNo !== '') {
                    return 'Dok. Kontrak';
                }
                return '-';

            default:
                // Jenis tanpa KIB type
                return '-';
        }
    }

    // Normalisasi nilai periode (tahunan, semester_1, semester_2)
    protected function normalizePeriode(string $periode): string
    {
        return match ($periode) {
            'semester', 'semester_1' => 'semester_1',
            'semester_2'             => 'semester_2',
            default                  => 'tahunan',
        };
    }

    // Resolve Perbekel untuk bagian TTD
    protected function getPerbekel(): ?array
    {
        // Prioritas 1: User dengan role perbekel
        $userPerbekel = \App\Models\User::with(['role', 'pejabat'])
            ->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))
            ->where('is_aktif', 1)
            ->first();

        if ($userPerbekel) {
            return [
                'nama'     => $userPerbekel->pejabat?->nama ?? $userPerbekel->nama,
                'jabatan'  => 'Perbekel ' . (config('desa.nama_lokasi') ?: 'Demo'),
                'ttd_path' => $userPerbekel->pejabat?->ttd_path ?? null,
            ];
        }

        // Prioritas 2: Pejabat dengan jabatan mengandung 'perbekel'
        $pejabat = Pejabat::where(function ($q) {
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

    // Resolve Kaur Tata Usaha dan Umum / Pengelola Aset untuk bagian TTD
    protected function getKaurTU(): ?array
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

        $pejabatKaurTU = Pejabat::where('jabatan', 'LIKE', '%kaur%')
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

    // Render HTML untuk export PDF Buku Inventaris Aset
    public function renderPdf(array $rows, int $tahun, string $periode, string $tanggalDokumen, bool $inclImg, string $paper = 'a4'): string
    {
        $desa = config('desa');
        $namaLokasi    = strtoupper($desa['nama_lokasi'] ?: ($desa['desa_nama'] ?: 'DEMO'));
        $namaProvinsi  = strtoupper($desa['provinsi_nama'] ?: 'BALI');
        $namaKabupaten = strtoupper($desa['kabupaten_nama'] ?: 'KABUPATEN DEMO');
        $namaKecamatan = strtoupper($desa['kecamatan_nama'] ?: 'KECAMATAN DEMO');
        $namaDesa      = strtoupper($desa['desa_nama'] ?: ($desa['nama_lokasi'] ?: 'DEMO'));

        $judulPeriode = match ($periode) {
            'semester_1' => 'SEMESTER I',
            'semester_2' => 'SEMESTER II',
            default      => 'TAHUNAN',
        };

        $pageSizeCss = \App\Services\ExportService::pageSizeCss($paper, 'landscape');
        $css = '
            @page { size: ' . $pageSizeCss . '; margin: 10mm 10mm 20mm 10mm; }
            body { font-family: Arial, sans-serif; font-size: 8.5px; color: #000; margin: 0; }
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8.5px; }
            .info-table td { border: none; padding: 1px 2px; }
            .title-block { text-align: center; margin-bottom: 12px; }
            .title-block .main-title { font-weight: bold; font-size: 11.5px; letter-spacing: 0.5px; margin-bottom: 2px; }
            .title-block .sub-title  { font-weight: bold; font-size: 10px; }
            table.data { width: 100%; border-collapse: collapse; font-size: 8px; clear: both; border: 1px solid #000; }
            table.data thead { display: table-header-group; }
            table.data tr { page-break-inside: avoid; }
            table.data th { border: 1px solid #000; padding: 4px 2px; text-align: center; font-weight: bold; vertical-align: middle; background: #9bc2e6; }
            table.data td { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }
            .num   { text-align: right; }
            .center{ text-align: center; }
            .bold  { font-weight: bold; }
            .total-row td { font-weight: bold; background: #9bc2e6; }
        ' . \App\Services\ExportService::buildFooterCss();

        $html = '<!doctype html><html><head><meta charset="utf-8"><style>' . $css . '</style></head><body>';

        // ── 1. Header kanan atas (info lokasi wilayah desa) ───────────────────
        $html .= '<table class="info-table" style="margin-bottom:14px;"><tr>';
        $html .= '<td style="width:68%; border:none;"></td>';
        $html .= '<td style="width:32%; border:none; vertical-align:top;">';
        $html .= '<table style="width:100%; font-size:8.5px; border-collapse:collapse;">';
        $html .= '<tr><td style="width:75px; font-weight:bold; border:none; padding:1px 0;">PROVINSI</td><td style="font-weight:bold; border:none; padding:1px 0;">: ' . $namaProvinsi . '</td></tr>';
        $html .= '<tr><td style="font-weight:bold; border:none; padding:1px 0;">KABUPATEN</td><td style="font-weight:bold; border:none; padding:1px 0;">: ' . $namaKabupaten . '</td></tr>';
        $html .= '<tr><td style="font-weight:bold; border:none; padding:1px 0;">KECAMATAN</td><td style="font-weight:bold; border:none; padding:1px 0;">: ' . $namaKecamatan . '</td></tr>';
        $html .= '<tr><td style="font-weight:bold; border:none; padding:1px 0;">DESA</td><td style="font-weight:bold; border:none; padding:1px 0;">: ' . $namaDesa . '</td></tr>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr></table>';

        // ── 2. Judul Utama (Center Penuh) ─────────────────────────────────────
        $html .= '<div class="title-block">';
        $html .= '<div class="main-title">BUKU INVENTARIS ASET DESA ' . $namaLokasi . '</div>';
        $html .= '<div class="sub-title">TAHUN ' . $tahun . ' (' . $judulPeriode . ')</div>';
        $html .= '</div>';

        // ── 3. Tabel Data ─────────────────────────────────────────────────────
        $html .= '<table class="data"><thead>';
        $html .= '<tr>';
        $html .= '<th style="width:3%;">NO</th>';
        $html .= '<th style="width:11%;">KODE BARANG</th>';
        $html .= '<th style="width:23%;">NAMA BARANG</th>';
        $html .= '<th style="width:4%;">NUP</th>';
        $html .= '<th style="width:11%;">MERK / TYPE</th>';
        $html .= '<th style="width:11%;">BUKTI KEPEMILIKAN</th>';
        $html .= '<th style="width:8%;">TAHUN<br>PEROLEHAN</th>';
        $html .= '<th style="width:7%;">UKURAN<br>M²</th>';
        $html .= '<th style="width:11%;">NILAI (Rp.)</th>';
        $html .= '<th style="width:11%;">KET</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $totalNilai = 0.0;
        foreach ($rows as $r) {
            $totalNilai += (float)($r['nilai'] ?? 0);
            $html .= '<tr>';
            $html .= '<td class="center">' . $r['no'] . '</td>';
            $html .= '<td class="center">' . htmlentities((string)($r['kode'] ?? '')) . '</td>';
            $html .= '<td>' . htmlentities((string)($r['nama_barang'] ?? '')) . '</td>';
            $html .= '<td class="center">' . htmlentities((string)($r['nup'] ?? '')) . '</td>';
            $html .= '<td>' . htmlentities((string)($r['merk_type'] ?? '')) . '</td>';
            $html .= '<td class="center">' . htmlentities((string)($r['bukti_kepemilikan'] ?? '-')) . '</td>';
            $html .= '<td class="center">' . htmlentities((string)($r['tahun_perolehan'] ?? '')) . '</td>';
            $html .= '<td class="center">' . htmlentities((string)($r['ukuran_m2'] ?? '')) . '</td>';
            $html .= '<td class="num">' . ($r['nilai'] > 0 ? number_format((float)$r['nilai'], fmod((float)$r['nilai'], 1) != 0 ? 2 : 0, ',', '.') : '-') . '</td>';
            $html .= '<td>' . htmlentities((string)($r['keterangan'] ?? '')) . '</td>';
            $html .= '</tr>';
        }

        // Baris NILAI ASET (total)
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="8" class="center bold" style="background:#9bc2e6;">NILAI ASET</td>';
        $html .= '<td class="num bold" style="background:#9bc2e6;">' . number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.') . '</td>';
        $html .= '<td style="background:#9bc2e6;"></td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';

        // ── 4. Tanda Tangan Resmi (Hanya Perbekel di Sisi Kanan) ───────────────
        $html .= $this->renderTtdPdf($inclImg, $tanggalDokumen);

        $html .= \App\Services\ExportService::buildFooterHtml('Buku Inventaris Aset Desa ' . ucwords(strtolower($namaLokasi)));
        $html .= '</body></html>';

        return $html;
    }

    // Render bagian tanda tangan PDF (Perbekel)
    protected function renderTtdPdf(bool $inclImg, string $tanggalDokumen): string
    {
        $perbekel = $this->getPerbekel();
        if (!$perbekel) {
            return '';
        }

        $namaLokasi = config('desa.nama_lokasi') ?: (config('desa.desa_nama') ?: 'Demo');
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        if (!empty($tanggalDokumen)) {
            try {
                $dt     = Carbon::parse($tanggalDokumen);
                $tglStr = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {
                $tglStr = date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
            }
        } else {
            $tglStr = date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
        }

        $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi . ', ' : '') . $tglStr;
        $jabatanPerbekel = htmlentities((string)($perbekel['jabatan'] ?? 'Perbekel ' . ucwords(strtolower($namaLokasi))));
        $namaPerbekel    = htmlentities((string)($perbekel['nama'] ?? ''));

        $html = '<div style="margin-top:24px; page-break-inside:avoid;">';
        $html .= '<table style="width:100%; border:none; border-collapse:collapse;"><tr>';
        $html .= '<td style="width:60%; border:none;"></td>';
        $html .= '<td style="width:40%; text-align:center; vertical-align:top; border:none; padding:4px 10px;">';
        $html .= '<div style="font-size:9px; margin-bottom:4px;">' . htmlentities($tanggalDisplay) . '</div>';
        $html .= '<div style="font-weight:bold; font-size:9px; margin-bottom:4px;">' . $jabatanPerbekel . ',</div>';

        if ($inclImg && !empty($perbekel['ttd_path'])) {
            $full = storage_path('app/public/' . ltrim($perbekel['ttd_path'], '/'));
            if (file_exists($full)) {
                $mime = mime_content_type($full) ?: 'image/png';
                $b64  = base64_encode(file_get_contents($full));
                $html .= '<img src="data:' . $mime . ';base64,' . $b64 . '" style="max-height:55px; max-width:140px; display:block; margin:6px auto;">';
            } else {
                $html .= '<div style="height:55px;"></div>';
            }
        } else {
            $html .= '<div style="height:55px;"></div>';
        }

        $html .= '<div style="text-decoration:underline; font-weight:bold; font-size:9px; margin-top:4px;">' . $namaPerbekel . '</div>';
        $html .= '</td>';
        $html .= '</tr></table>';
        $html .= '</div>';

        return $html;
    }

    // ── PDF SAVE ───────────────────────────────────────────────────────────────

    protected function savePdf(string $html, string $relativePath, string $paper = 'a4'): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        // Pastikan font dir ada dan writable, jika gagal gunakan temp dir
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0775, true);
        }
        if (!is_writable($fontDir)) {
            $fontDir = sys_get_temp_dir();
        }

        // Pastikan target directory export tersedia
        $targetDir = dirname(Storage::disk($this->disk)->path($relativePath));
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        [$size, $orientation] = match(strtolower($paper)) {
            'legal'  => ['legal', 'landscape'],
            'letter' => ['letter', 'landscape'],
            'f4'     => [[0, 0, 595.28, 935.43], 'landscape'],
            default  => ['a4', 'landscape'],
        };

        $dompdfOptions = [
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => true,
            'isPhpEnabled'            => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont'             => 'sans-serif',
            'fontDir'                 => $fontDir,
            'fontCache'               => $fontDir,
            'tempDir'                 => sys_get_temp_dir(),
            'chroot'                  => [base_path(), storage_path(), sys_get_temp_dir()],
        ];

        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setOptions($dompdfOptions);
            $pdf->setPaper($size, $orientation);
            Storage::disk($this->disk)->put($relativePath, $pdf->output());
        } elseif (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf($dompdfOptions);
            $dompdf->loadHtml($html);
            $dompdf->setPaper($size, $orientation);
            $dompdf->render();
            Storage::disk($this->disk)->put($relativePath, $dompdf->output());
        } else {
            // Fallback: simpan sebagai HTML
            Storage::disk($this->disk)->put($relativePath, $html);
        }
    }
}
