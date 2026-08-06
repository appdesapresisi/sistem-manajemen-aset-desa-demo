<?php

namespace App\Services;

use App\Models\Inventaris;
use App\Models\InventarisDeletedLog;
use App\Models\ExportLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LaporanAsetService
{
    protected string $disk = 'local';

    /**
     * Export laporan aset per tahun atau per semester.
     *
     * @param array $opts
     *   - tahun: int (wajib)
     *   - periode: 'tahunan' | 'semester'
     *   - format: 'excel' | 'pdf'
     *   - user_id: int
     */
    public function export(array $opts): array
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        $tahun   = (int) ($opts['tahun'] ?? date('Y'));
        $periode = $opts['periode'] ?? 'tahunan';
        if ($periode === 'semester') {
            $periode = 'semester_1';
        }
        $format  = strtolower($opts['format'] ?? 'excel');
        $userId  = $opts['user_id'] ?? null;

        // Tentukan label akhir periode
        if ($periode === 'semester_1') {
            $labelAkhir = '30 JUNI ' . $tahun;
            $slug       = 'laporan_aset_semester_1_' . $tahun;
        } elseif ($periode === 'semester_2') {
            $labelAkhir = '31 DESEMBER ' . $tahun;
            $slug       = 'laporan_aset_semester_2_' . $tahun;
        } else {
            $labelAkhir = '31 DESEMBER ' . $tahun;
            $slug       = 'laporan_aset_tahunan_' . $tahun;
        }

        $rows = $this->buildRows($tahun, $periode);

        $baseDir = 'exports/' . Carbon::now()->format('Y/m');
        Storage::disk($this->disk)->makeDirectory($baseDir);
        $timestamp = Carbon::now()->format('Ymd_His');
        $filename  = $slug . '_' . $timestamp . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
        $relativePath = $baseDir . '/' . $filename;

        if ($format === 'excel') {
            $sheet = new \App\Exports\LaporanAsetExport($rows, $tahun, $periode, $labelAkhir);
            \Maatwebsite\Excel\Facades\Excel::store($sheet, $relativePath, $this->disk);
        } else {
            $html = $this->renderPdf($rows, $tahun, $periode, $labelAkhir);
            $this->savePdf($html, $relativePath);
        }

        $log = ExportLog::create([
            'user_id'          => $userId,
            'tipe_export'      => $format,
            'nama_file'        => $filename,
            'pejabat_override' => [],
            'filters'          => ['tahun' => $tahun, 'periode' => $periode, 'mode' => 'laporan_aset'],
        ]);

        return ['filename' => $filename, 'path' => $relativePath, 'log_id' => $log->id ?? null];
    }

    /**
     * Bangun baris data laporan.
     * Grouping: kode_barang + nup + nama_barang
     */
    public function buildRows(int $tahun, string $periode): array
    {
        if ($periode === 'semester') {
            $periode = 'semester_1';
        }

        // 1. SALDO AWAL
        if ($periode === 'semester_2') {
            // Saldo per 1 Juli = Active assets acquired before July 1st of $tahun
            // PERUBAHAN: Filter berdasarkan bulan_perolehan, bukan created_at
            $saldoAwalActive = Inventaris::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->with('laporanAsetValue')
                ->where(function($q) use ($tahun) {
                    $q->where('tahun_perolehan', '<', $tahun)
                      ->orWhere(function($sq) use ($tahun) {
                          $sq->where('tahun_perolehan', $tahun)
                             ->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '<=', 6));
                      })
                      // Jika tidak ada bulan_perolehan, anggap Semester 1 (default)
                      ->orWhere(function($sq) use ($tahun) {
                          $sq->where('tahun_perolehan', $tahun)
                             ->whereDoesntHave('laporanAsetValue');
                      });
                })
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);

            $saldoAwalDeleted = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->where('deleted_at', '>=', "$tahun-07-01 00:00:00")
                ->where(function($q) use ($tahun) {
                    $q->where('tahun_perolehan', '<', $tahun)
                      ->orWhere('tahun_perolehan', $tahun);
                })
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        } else {
            // Per Tahun atau Semester I (Saldo Awal per 1 Januari)
            $saldoAwalActive = Inventaris::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->with('laporanAsetValue')
                ->where('tahun_perolehan', '<', $tahun)
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);

            $saldoAwalDeleted = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->where('tahun_perolehan', '<', $tahun)
                ->where('deleted_at', '>=', "$tahun-01-01 00:00:00")
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        }

        // Combine active and deleted for Saldo Awal
        $saldoAwal = $saldoAwalActive->concat($saldoAwalDeleted);

        // 2. MUTASI BERTAMBAH
        // PERUBAHAN: Filter berdasarkan bulan_perolehan, bukan created_at
        if ($periode === 'semester_1') {
            // Acquired in $tahun and acquired month in Semester I (bulan 1-6) or NULL (default Semester 1)
            $bertambahActive = Inventaris::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->with('laporanAsetValue')
                ->where('tahun_perolehan', $tahun)
                ->where(function($q) {
                    // Bulan perolehan 1-6 (Semester 1)
                    $q->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '<=', 6))
                      // Atau tidak ada bulan_perolehan (default Semester 1)
                      ->orWhereDoesntHave('laporanAsetValue');
                })
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);

            // Deleted assets acquired in $tahun: assume Semester 1 (convention)
            $bertambahDeleted = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->where('tahun_perolehan', $tahun)
                ->where('deleted_at', '>=', "$tahun-01-01 00:00:00")
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        } elseif ($periode === 'semester_2') {
            // Acquired in $tahun and acquired month in Semester II (bulan 7-12)
            $bertambahActive = Inventaris::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->with('laporanAsetValue')
                ->where('tahun_perolehan', $tahun)
                ->whereHas('laporanAsetValue', fn($lq) => $lq->where('bulan_perolehan', '>=', 7))
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);

            $bertambahDeleted = collect();
        } else {
            // Per Tahun - semua aset dengan tahun_perolehan = $tahun
            $bertambahActive = Inventaris::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->with('laporanAsetValue')
                ->where('tahun_perolehan', $tahun)
                ->whereHas('jenis', fn($q) => $q->where('nama', '!=', \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN))
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);

            $bertambahDeleted = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->where('tahun_perolehan', $tahun)
                ->where('deleted_at', '>=', "$tahun-01-01 00:00:00")
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        }

        // Combine active and deleted for Bertambah
        $bertambah = $bertambahActive->concat($bertambahDeleted);

        // 3. MUTASI BERKURANG
        if ($periode === 'semester_1') {
            $berkurang = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->whereYear('deleted_at', $tahun)
                ->whereMonth('deleted_at', '<=', 6)
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        } elseif ($periode === 'semester_2') {
            $berkurang = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->whereYear('deleted_at', $tahun)
                ->whereMonth('deleted_at', '>=', 7)
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        } else {
            // Per Tahun
            $berkurang = InventarisDeletedLog::whereNotNull('kode_barang')
                ->whereNotNull('nama_barang')
                ->whereYear('deleted_at', $tahun)
                ->get(['kode_barang', 'nup', 'nama_barang', 'satuan', 'nilai_perolehan', 'tahun_perolehan']);
        }

        // Kumpulkan semua grup unik
        $groups = collect();

        foreach ([$saldoAwal, $bertambah, $berkurang] as $collection) {
            foreach ($collection as $item) {
                // Grouping key: kode_barang + nama_barang + tahun_perolehan
                $key = trim((string)($item->kode_barang ?? ''))
                     . '||' . trim((string)($item->nama_barang ?? ''))
                     . '||' . trim((string)($item->tahun_perolehan ?? ''));
                if (!$groups->has($key)) {
                    $groups->put($key, [
                        'kode'        => $item->kode_barang ?? '',
                        'nama'        => $item->nama_barang ?? '',
                        'satuan'      => $item->satuan ?? 'Unit',
                        'saldo_kwant' => 0,
                        'saldo_nilai' => 0.0,
                        'tambah_kwant'=> 0,
                        'tambah_nilai'=> 0.0,
                        'kurang_kwant'=> 0,
                        'kurang_nilai'=> 0.0,
                    ]);
                }
            }
        }

        // Isi saldo awal
        foreach ($saldoAwal as $item) {
            $key = trim((string)($item->kode_barang ?? ''))
                 . '||' . trim((string)($item->nama_barang ?? ''))
                 . '||' . trim((string)($item->tahun_perolehan ?? ''));
            if ($groups->has($key)) {
                $g = $groups->get($key);
                $g['saldo_kwant'] += 1;
                $g['saldo_nilai'] += (float)($item->nilai_perolehan ?? 0);
                if (empty($g['satuan']) || $g['satuan'] === 'Unit') {
                    $g['satuan'] = $item->satuan ?? 'Unit';
                }
                $groups->put($key, $g);
            }
        }

        // Isi mutasi bertambah
        foreach ($bertambah as $item) {
            $key = trim((string)($item->kode_barang ?? ''))
                 . '||' . trim((string)($item->nama_barang ?? ''))
                 . '||' . trim((string)($item->tahun_perolehan ?? ''));
            if ($groups->has($key)) {
                $g = $groups->get($key);
                $g['tambah_kwant'] += 1;
                $g['tambah_nilai'] += (float)($item->nilai_perolehan ?? 0);
                $groups->put($key, $g);
            }
        }

        // Isi mutasi berkurang
        foreach ($berkurang as $item) {
            $key = trim((string)($item->kode_barang ?? ''))
                 . '||' . trim((string)($item->nama_barang ?? ''))
                 . '||' . trim((string)($item->tahun_perolehan ?? ''));
            if ($groups->has($key)) {
                $g = $groups->get($key);
                $g['kurang_kwant'] += 1;
                $g['kurang_nilai'] += (float)($item->nilai_perolehan ?? 0);
                $groups->put($key, $g);
            }
        }

        // Hitung saldo akhir dan return sebagai array
        return $groups->values()->map(function ($g) {
            $g['akhir_kwant'] = $g['saldo_kwant'] + $g['tambah_kwant'] - $g['kurang_kwant'];
            $g['akhir_nilai'] = $g['saldo_nilai'] + $g['tambah_nilai'] - $g['kurang_nilai'];
            return $g;
        })->sortBy('kode')->values()->toArray();
    }

    protected function renderPdf(array $rows, int $tahun, string $periode, string $labelAkhir): string
    {
        $desa = config('desa');
        if ($periode === 'semester_1') {
            $judulPeriode = 'SEMESTER I';
        } elseif ($periode === 'semester_2') {
            $judulPeriode = 'SEMESTER II';
        } else {
            $judulPeriode = 'PERTAHUN';
        }

        $css = '
            @page { size: A4 landscape; margin: 10mm 10mm 22mm 10mm; }
            body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; }
            .title { text-align:center; font-weight:bold; font-size:11px; margin-bottom:0; }
            table { width:100%; border-collapse:collapse; font-size:8px; }
            th { border:1px solid #000; padding:3px 4px; text-align:center; font-weight:bold; vertical-align:middle; background:#fff; }
            td { border:1px solid #000; padding:2px 4px; vertical-align:middle; }
            .num { text-align:right; }
            .center { text-align:center; }
            .bold { font-weight:bold; }
        ' . \App\Services\ExportService::buildFooterCss();

        $html = '<!doctype html><html><head><meta charset="utf-8"><style>' . $css . '</style></head><body>';
        $html .= '<div class="title">LAPORAN ASET DESA ' . strtoupper($desa['nama_lokasi'] ?? '') . ' ' . $judulPeriode . '</div>';
        $html .= '<div class="title">RINCIAN PENGELOMPOKAN BARANG</div>';
        $html .= '<div class="title">TAHUN ANGGARAN ' . $tahun . '</div>';
        $html .= '<div style="margin-bottom:45px;"></div>';

        $html .= '<table><thead>';
        $html .= '<tr>';
        $html .= '<th rowspan="3">KODE</th>';
        $html .= '<th rowspan="3">NAMA BARANG</th>';
        $html .= '<th rowspan="3">SATUAN</th>';
        $tanggalSaldoAwal = ($periode === 'semester_2') ? '1 JULI ' : '1 JANUARI ';
        $html .= '<th colspan="2" rowspan="2">SALDO PER ' . $tanggalSaldoAwal . $tahun . '</th>';
        $html .= '<th colspan="4">MUTASI</th>';
        $html .= '<th colspan="2" rowspan="2">SALDO PER ' . strtoupper($labelAkhir) . '</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th colspan="2">BERTAMBAH</th>';
        $html .= '<th colspan="2">BERKURANG</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th>KWANTITAS</th><th>NILAI (RP)</th>';
        $html .= '<th>KWANTITAS</th><th>NILAI (RP)</th>';
        $html .= '<th>KWANTITAS</th><th>NILAI (RP)</th>';
        $html .= '<th>KWANTITAS</th><th>NILAI (RP)</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th class="center">1</th><th class="center">2</th><th class="center">3</th>';
        $html .= '<th class="center">4</th><th class="center">5</th>';
        $html .= '<th class="center">6</th><th class="center">7</th>';
        $html .= '<th class="center">8</th><th class="center">9</th>';
        $html .= '<th class="center">10</th><th class="center">11</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $totSaldoK = $totSaldoN = $totTambahK = $totTambahN = $totKurangK = $totKurangN = $totAkhirK = $totAkhirN = 0;

        foreach ($rows as $r) {
            $totSaldoK  += $r['saldo_kwant'];
            $totSaldoN  += $r['saldo_nilai'];
            $totTambahK += $r['tambah_kwant'];
            $totTambahN += $r['tambah_nilai'];
            $totKurangK += $r['kurang_kwant'];
            $totKurangN += $r['kurang_nilai'];
            $totAkhirK  += $r['akhir_kwant'];
            $totAkhirN  += $r['akhir_nilai'];

            $html .= '<tr>';
            $html .= '<td>' . htmlentities($r['kode']) . '</td>';
            $html .= '<td>' . htmlentities($r['nama']) . '</td>';
            $html .= '<td class="center">' . htmlentities($r['satuan']) . '</td>';
            $html .= '<td class="center">' . ($r['saldo_kwant'] ?: '-') . '</td>';
            $html .= '<td class="num">' . ($r['saldo_nilai'] > 0 ? number_format($r['saldo_nilai'], 2, ',', '.') : '-') . '</td>';
            $html .= '<td class="center">' . ($r['tambah_kwant'] ?: '-') . '</td>';
            $html .= '<td class="num">' . ($r['tambah_nilai'] > 0 ? number_format($r['tambah_nilai'], 2, ',', '.') : '-') . '</td>';
            $html .= '<td class="center">' . ($r['kurang_kwant'] ?: '-') . '</td>';
            $html .= '<td class="num">' . ($r['kurang_nilai'] > 0 ? number_format($r['kurang_nilai'], 2, ',', '.') : '-') . '</td>';
            $html .= '<td class="center">' . ($r['akhir_kwant'] ?: '-') . '</td>';
            $html .= '<td class="num">' . ($r['akhir_nilai'] > 0 ? number_format($r['akhir_nilai'], 2, ',', '.') : '-') . '</td>';
            $html .= '</tr>';
        }

        // Baris JUMLAH - Hanya untuk kolom NILAI, KWANTITAS dikosongkan
        $html .= '<tr class="bold">';
        $html .= '<td colspan="3" class="center bold">JUMLAH</td>';
        $html .= '<td class="center bold"></td>'; // KWANTITAS kosong
        $html .= '<td class="num bold">' . number_format($totSaldoN, 2, ',', '.') . '</td>';
        $html .= '<td class="center bold"></td>'; // KWANTITAS kosong
        $html .= '<td class="num bold">' . number_format($totTambahN, 2, ',', '.') . '</td>';
        $html .= '<td class="center bold"></td>'; // KWANTITAS kosong
        $html .= '<td class="num bold">' . number_format($totKurangN, 2, ',', '.') . '</td>';
        $html .= '<td class="center bold"></td>'; // KWANTITAS kosong
        $html .= '<td class="num bold">' . number_format($totAkhirN, 2, ',', '.') . '</td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';
        
        // Tambah tanda tangan
        $pejabats = $this->getPejabatForLaporanAset();
        $html .= $this->renderLaporanAsetTtd($pejabats, true);
        
        $html .= \App\Services\ExportService::buildFooterHtml('Laporan Aset ' . ($periode === 'semester_1' ? 'Semester I' : ($periode === 'semester_2' ? 'Semester II' : 'Per Tahun')));
        $html .= '</body></html>';
        return $html;
    }

    /**
     * Get pejabat data untuk Laporan Aset (Perbekel & Kaur TU)
     * Prioritas: 1) User by role+bidang, 2) Pejabat by jabatan
     */
    protected function getPejabatForLaporanAset(): array
    {
        // === PERBEKEL ===
        $perbekel = null;
        
        // Prioritas 1: Cari di Users dengan role 'perbekel'
        $userPerbekel = \App\Models\User::with('role')
            ->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))
            ->where('is_aktif', 1)
            ->first();
        
        if ($userPerbekel) {
            $perbekel = [
                'nama' => $userPerbekel->nama,
                'jabatan' => 'Perbekel ' . (config('desa.nama_lokasi') ?: 'Mengwi'),
                'ttd_path' => null,
            ];
        } else {
            // Prioritas 2: Cari di Pejabats dengan jabatan mengandung 'perbekel'
            $pejabatPerbekel = \App\Models\Pejabat::where(function($q) {
                $q->where('jabatan', 'LIKE', '%perbekel%')
                  ->orWhere('jabatan', 'LIKE', '%kepala desa%');
            })->first();
            
            if ($pejabatPerbekel) {
                $perbekel = [
                    'nama' => $pejabatPerbekel->nama,
                    'jabatan' => $pejabatPerbekel->jabatan,
                    'ttd_path' => $pejabatPerbekel->ttd_path ?? null,
                ];
            }
        }
        
        // === KAUR TATA USAHA DAN UMUM ===
        $kaurTU = null;
        
        // Prioritas 1: Cari di Users dengan role 'kaur' DAN bidang 'Tata Usaha dan Umum'
        $userKaurTU = \App\Models\User::with(['role', 'bidang'])
            ->whereHas('role', fn($q) => $q->where('nama', 'kaur'))
            ->whereHas('bidang', fn($q) => $q->where('nama', 'LIKE', '%tata usaha%')
                                             ->orWhere('nama', 'LIKE', '%umum%'))
            ->where('is_aktif', 1)
            ->first();
        
        if ($userKaurTU) {
            $bidangNama = $userKaurTU->bidang->nama ?? 'Kaur Umum';
            $kaurTU = [
                'nama' => $userKaurTU->nama,
                'jabatan' => 'Pengelola Aset ( Kaur Umum)',
                'ttd_path' => null,
            ];
        } else {
            // Prioritas 2: Cari di Pejabats dengan jabatan mengandung 'kaur' dan 'tata usaha' / 'umum'
            $pejabatKaurTU = \App\Models\Pejabat::where('jabatan', 'LIKE', '%kaur%')
                ->where(function($q) {
                    $q->where('jabatan', 'LIKE', '%tata usaha%')
                      ->orWhere('jabatan', 'LIKE', '%umum%');
                })
                ->first();
            
            if ($pejabatKaurTU) {
                $kaurTU = [
                    'nama' => $pejabatKaurTU->nama,
                    'jabatan' => $pejabatKaurTU->jabatan,
                    'ttd_path' => $pejabatKaurTU->ttd_path ?? null,
                ];
            }
        }
        
        return [
            'perbekel' => $perbekel,
            'kaur_tu' => $kaurTU,
        ];
    }

    /**
     * Render tanda tangan untuk Laporan Aset (2 kolom: Perbekel & Kaur TU)
     */
    protected function renderLaporanAsetTtd(array $pejabats, bool $includeImg, string $tanggalDokumen = ''): string
    {
        $perbekelNama = trim((string)($pejabats['perbekel']['nama'] ?? ''));
        $kaurTUNama   = trim((string)($pejabats['kaur_tu']['nama'] ?? ''));
        if ($perbekelNama === '' && $kaurTUNama === '') {
            return '';
        }

        $namaLokasi = config('desa.nama_lokasi', '');
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        if (!empty($tanggalDokumen)) {
            try {
                $dt = \Carbon\Carbon::parse($tanggalDokumen);
                $tglStr = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            } catch (\Throwable $e) {
                $tglStr = date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
            }
        } else {
            $tglStr = date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
        }
        
        $tanggalDisplay = ($namaLokasi !== '' ? $namaLokasi . ', ' : '') . $tglStr;
        
        $html = '<div style="margin-top:30px;page-break-inside:avoid;"><table style="width:100%;border:none;"><tr>';
        
        // Kolom Kiri: Mengetahui, Perbekel
        $html .= '<td style="width:50%;text-align:center;border:none;padding:8px;">';
        
        $perbekel = $pejabats['perbekel'] ?? null;
        if ($perbekel) {
            $html .= '<div style="font-weight:bold;margin-bottom:4px;">Mengetahui,<br>' . htmlentities($perbekel['jabatan'] ?? 'Perbekel ' . (config('desa.nama_lokasi') ?: 'Mengwi')) . '</div>';
            
            if ($includeImg && !empty($perbekel['ttd_path'])) {
                $full = storage_path('app/public/' . ltrim($perbekel['ttd_path'], '/'));
                if (file_exists($full)) {
                    $mime = mime_content_type($full) ?: 'image/png';
                    $html .= '<img src="data:' . $mime . ';base64,' . base64_encode(file_get_contents($full)) . '" style="max-height:60px;max-width:150px;display:block;margin:8px auto;">';
                } else {
                    $html .= '<div style="height:60px;"></div>';
                }
            } else {
                $html .= '<div style="height:60px;"></div>';
            }
            
            $html .= '<div style="text-decoration:underline;font-weight:bold;margin-top:4px;">' . htmlentities($perbekel['nama']) . '</div>';
        }
        
        $html .= '</td>';
        
        // Kolom Kanan: Tanggal + Pengelola Aset (Kaur Umum)
        $html .= '<td style="width:50%;text-align:center;border:none;padding:8px;">';
        
        $kaurTU = $pejabats['kaur_tu'] ?? null;
        if ($kaurTU) {
            $html .= '<div style="font-size:9px;margin-bottom:4px;">' . htmlentities($tanggalDisplay) . '</div>';
            $html .= '<div style="font-weight:bold;margin-bottom:4px;">Pengelola Aset ( Kaur Umum)</div>';
            
            if ($includeImg && !empty($kaurTU['ttd_path'])) {
                $full = storage_path('app/public/' . ltrim($kaurTU['ttd_path'], '/'));
                if (file_exists($full)) {
                    $mime = mime_content_type($full) ?: 'image/png';
                    $html .= '<img src="data:' . $mime . ';base64,' . base64_encode(file_get_contents($full)) . '" style="max-height:60px;max-width:150px;display:block;margin:8px auto;">';
                } else {
                    $html .= '<div style="height:60px;"></div>';
                }
            } else {
                $html .= '<div style="height:60px;"></div>';
            }
            
            $html .= '<div style="text-decoration:underline;font-weight:bold;margin-top:4px;">' . htmlentities($kaurTU['nama']) . '</div>';
        }
        
        $html .= '</td>';
        
        $html .= '</tr></table></div>';
        
        return $html;
    }

    protected function savePdf(string $html, string $relativePath): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);

        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            Storage::disk($this->disk)->put($relativePath, $pdf->output());
        } elseif (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            Storage::disk($this->disk)->put($relativePath, $dompdf->output());
        } else {
            Storage::disk($this->disk)->put($relativePath, $html);
        }
    }
}
