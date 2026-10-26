<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pejabat;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Ruangan;
use App\Models\ExportLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display dashboard with KPI data
     */
    public function index()
    {
        $kpis = $this->getKpiDataArray(true);
        $chartData = $this->getChartDataArray(true);
        $activities = $this->getRecentActivitiesArray();
        $exportLogs = $this->getRecentExportsArray();
        $jenisInventarisList = $this->getJenisInventarisListArray();
        $rentangNilai = $this->getRentangNilaiArray();
        $tahunPerolehanData = $this->getTahunPerolehanDataArray();
        
        $attentionAssets = Inventaris::with(['jenis', 'ruangan'])
            ->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'rusak_ringan', 'rusak_berat'])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();
            
        $recentAssets = Inventaris::with(['jenis', 'ruangan'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $topRuangan = Ruangan::withCount('inventaris')
            ->orderByDesc('inventaris_count')
            ->limit(4)
            ->get();
        $ruanganTerisiCount = Ruangan::has('inventaris')->count();
        $totalRuangan = Ruangan::count();

        return view('dashboard.index', compact(
            'kpis',
            'chartData',
            'activities',
            'exportLogs',
            'jenisInventarisList',
            'rentangNilai',
            'tahunPerolehanData',
            'attentionAssets',
            'recentAssets',
            'topRuangan',
            'ruanganTerisiCount',
            'totalRuangan'
        ));
    }

    /**
     * Data distribusi aset menurut rentang nilai perolehan (dinamis).
     * Penggolongan menggunakan harga per unit (nilai_perolehan / kuantitas jika ada).
     * Batas rentang: batas bawah inklusif, batas atas eksklusif.
     */
    private function getRentangNilaiArray(): array
    {
        $approvedBase = $this->approvedInventarisQuery()
            ->select('id', 'nama_barang', 'nilai_perolehan')
            ->get();

        $ranges = [
            'above_50m' => [
                'key' => 'above_50m',
                'label' => 'Di atas Rp 50 juta',
                'sub' => '≥ Rp 50.000.000 / unit',
                'unit' => 0,
                'total_nilai' => 0,
            ],
            '10m_to_50m' => [
                'key' => '10m_to_50m',
                'label' => 'Rp 10–50 juta',
                'sub' => 'Rp 10.000.000 s/d < Rp 50.000.000 / unit',
                'unit' => 0,
                'total_nilai' => 0,
            ],
            '1m_to_10m' => [
                'key' => '1m_to_10m',
                'label' => 'Rp 1–10 juta',
                'sub' => 'Rp 1.000.000 s/d < Rp 10.000.000 / unit',
                'unit' => 0,
                'total_nilai' => 0,
            ],
            'below_1m' => [
                'key' => 'below_1m',
                'label' => 'Di bawah Rp 1 juta',
                'sub' => '< Rp 1.000.000 / unit',
                'unit' => 0,
                'total_nilai' => 0,
            ],
        ];

        foreach ($approvedBase as $item) {
            $qty = 1;
            if (preg_match('/\((\d+)\s*(unit|buah|set|bh)?\)/i', $item->nama_barang, $m)) {
                $parsed = (int) $m[1];
                if ($parsed > 0) {
                    $qty = $parsed;
                }
            }
            $val = (float) $item->nilai_perolehan;
            $unitPrice = $qty > 0 ? ($val / $qty) : $val;

            if ($unitPrice >= 50000000) {
                $ranges['above_50m']['unit']++;
                $ranges['above_50m']['total_nilai'] += $val;
            } elseif ($unitPrice >= 10000000) {
                $ranges['10m_to_50m']['unit']++;
                $ranges['10m_to_50m']['total_nilai'] += $val;
            } elseif ($unitPrice >= 1000000) {
                $ranges['1m_to_10m']['unit']++;
                $ranges['1m_to_10m']['total_nilai'] += $val;
            } else {
                $ranges['below_1m']['unit']++;
                $ranges['below_1m']['total_nilai'] += $val;
            }
        }

        foreach ($ranges as &$r) {
            $miliar = $r['total_nilai'] / 1000000000;
            if ($miliar >= 1) {
                $r['formatted_nilai'] = 'Rp ' . number_format($miliar, 1, ',', '.') . ' miliar';
            } elseif ($r['total_nilai'] >= 1000000) {
                $juta = $r['total_nilai'] / 1000000;
                $r['formatted_nilai'] = 'Rp ' . number_format($juta, 1, ',', '.') . ' juta';
            } else {
                $r['formatted_nilai'] = 'Rp ' . number_format($r['total_nilai'], 0, ',', '.');
            }
            $r['full_nominal'] = 'Rp ' . number_format($r['total_nilai'], 0, ',', '.');
        }
        unset($r);

        return $ranges;
    }

    /**
     * Data perolehan aset per tahun (dinamis dari database).
     */
    private function getTahunPerolehanDataArray(): array
    {
        $approvedBase = $this->approvedInventarisQuery();
        $years = (clone $approvedBase)
            ->select('tahun_perolehan', DB::raw('COUNT(*) as count'), DB::raw('SUM(nilai_perolehan) as total_nilai'))
            ->whereNotNull('tahun_perolehan')
            ->groupBy('tahun_perolehan')
            ->orderBy('tahun_perolehan')
            ->get();

        $pre2016Count = $years->where('tahun_perolehan', '<', 2016)->sum('count');
        $pre2016Nilai = $years->where('tahun_perolehan', '<', 2016)->sum('total_nilai');

        $labels = ['≤ 2015'];
        $counts = [(int) $pre2016Count];
        $values = [(float) $pre2016Nilai];

        $currentYear = (int) date('Y');
        for ($y = 2016; $y <= $currentYear; $y++) {
            $row = $years->firstWhere('tahun_perolehan', $y);
            $labels[] = (string) $y;
            $counts[] = $row ? (int) $row->count : 0;
            $values[] = $row ? (float) $row->total_nilai : 0;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'values' => $values,
            'total_aset' => array_sum($counts),
        ];
    }

    /**
     * Ambil semua jenis inventaris beserta jumlah inventarisnya (dinamis)
     */
    private function getJenisInventarisListArray()
    {
        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();

        return JenisInventaris::withCount('inventaris')
            ->where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('id')
            ->get()
            ->map(function($jenis) use ($todayStart, $yesterdayStart) {
                $todayCount = Inventaris::where('jenis_inventaris_id', $jenis->id)
                    ->where('created_at', '>=', $todayStart)
                    ->count();
                $yesterdayCount = Inventaris::where('jenis_inventaris_id', $jenis->id)
                    ->whereBetween('created_at', [$yesterdayStart, $todayStart])
                    ->count();
                $diff = $todayCount - $yesterdayCount;
                $percent = $yesterdayCount > 0
                    ? round(($diff / $yesterdayCount) * 100, 1)
                    : ($todayCount > 0 ? 100 : 0);

                $jenis->trend_diff = $diff;
                $jenis->trend_percent = $percent;
                return $jenis;
            });
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        return JenisInventaris::withCount('inventaris')
            ->withSum('inventaris as total_nilai', 'nilai_perolehan')
            ->where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('id')
            ->get()
            ->map(function($jenis) use ($thisMonthStart, $lastMonthStart) {
                $thisMonthCount = Inventaris::where('jenis_inventaris_id', $jenis->id)
                    ->where('created_at', '>=', $thisMonthStart)
                    ->count();
                $lastMonthCount = Inventaris::where('jenis_inventaris_id', $jenis->id)
                    ->where('created_at', '>=', $lastMonthStart)
                    ->where('created_at', '<', $thisMonthStart)
                    ->count();
                $diff = $thisMonthCount - $lastMonthCount;
                $percent = $lastMonthCount > 0
                    ? round(($diff / $lastMonthCount) * 100, 1)
                    : ($thisMonthCount > 0 ? 100 : 0);

                $jenis->trend_diff = $diff;
                $jenis->trend_percent = $percent;
                return $jenis;
            });
    }

    /**
     * Query dasar inventaris yang sudah disetujui untuk kebutuhan dashboard.
     */
    private function approvedInventarisQuery()
    {
        // Approval workflow is disabled; use all inventaris records for dashboard metrics
        return Inventaris::query();
    }

    private function getKpiDataArray(bool $fresh = false)
    {
        if ($fresh) {
            Cache::forget('dashboard_kpi');
        }

        return Cache::remember('dashboard_kpi', 30, function() {
            $approvedBase = $this->approvedInventarisQuery();

            /*
            // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
            $todayStart = now()->startOfDay();
            $yesterdayStart = now()->subDay()->startOfDay();

            // Total Aset
            $totalAssets = (clone $approvedBase)->count();
            $assetsToday = (clone $approvedBase)->where('created_at', '>=', $todayStart)->count();
            $assetsYesterday = (clone $approvedBase)->whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
            $assetsDiff = $assetsToday - $assetsYesterday;
            $assetsPercent = $assetsYesterday > 0 ? round(($assetsDiff / $assetsYesterday) * 100, 1) : ($assetsToday > 0 ? 100 : 0);

            // Total Nilai Aset
            $totalValue = (clone $approvedBase)->sum('nilai_perolehan') ?? 0;
            $valueToday = (clone $approvedBase)->where('created_at', '>=', $todayStart)->sum('nilai_perolehan') ?? 0;
            $valueYesterday = (clone $approvedBase)->whereBetween('created_at', [$yesterdayStart, $todayStart])->sum('nilai_perolehan') ?? 0;
            $valueDiff = $valueToday - $valueYesterday;
            $valuePercent = $valueYesterday > 0 ? round(($valueDiff / $valueYesterday) * 100, 1) : ($valueToday > 0 ? 100 : 0);

            // Total Pejabat Aktif
            $totalPejabat = Pejabat::where('is_aktif', 1)->count();
            $pejabatToday = Pejabat::where('is_aktif', 1)->where('created_at', '>=', $todayStart)->count();
            $pejabatYesterday = Pejabat::where('is_aktif', 1)->whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
            $pejabatDiff = $pejabatToday - $pejabatYesterday;
            $pejabatPercent = $pejabatYesterday > 0 ? round(($pejabatDiff / $pejabatYesterday) * 100, 1) : ($pejabatToday > 0 ? 100 : 0);

            // Total Users
            $totalUsers = User::count();
            $usersToday = User::where('created_at', '>=', $todayStart)->count();
            $usersYesterday = User::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
            $usersDiff = $usersToday - $usersYesterday;
            $usersPercent = $usersYesterday > 0 ? round(($usersDiff / $usersYesterday) * 100, 1) : ($usersToday > 0 ? 100 : 0);
            */

            // Konteks Per Bulan (vs. Bulan Lalu)
            $thisMonthStart = now()->startOfMonth();
            $lastMonthStart = now()->subMonth()->startOfMonth();

            // Total Aset
            $totalAssets = (clone $approvedBase)->count();
            $assetsThisMonth = (clone $approvedBase)->where('created_at', '>=', $thisMonthStart)->count();
            $assetsLastMonth = (clone $approvedBase)->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
            $assetsDiff = $assetsThisMonth - $assetsLastMonth;
            $assetsPercent = $assetsLastMonth > 0 ? round(($assetsDiff / $assetsLastMonth) * 100, 1) : ($assetsThisMonth > 0 ? 100 : 0);

            // Total Nilai Aset
            $totalValue = (clone $approvedBase)->sum('nilai_perolehan') ?? 0;
            $valueThisMonth = (clone $approvedBase)->where('created_at', '>=', $thisMonthStart)->sum('nilai_perolehan') ?? 0;
            $valueLastMonth = (clone $approvedBase)->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->sum('nilai_perolehan') ?? 0;
            $valueDiff = $valueThisMonth - $valueLastMonth;
            $valuePercent = $valueLastMonth > 0 ? round(($valueDiff / $valueLastMonth) * 100, 1) : ($valueThisMonth > 0 ? 100 : 0);

            // Total Pejabat Aktif
            $totalPejabat = Pejabat::where('is_aktif', 1)->count();
            $pejabatThisMonth = Pejabat::where('is_aktif', 1)->where('created_at', '>=', $thisMonthStart)->count();
            $pejabatLastMonth = Pejabat::where('is_aktif', 1)->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
            $pejabatDiff = $pejabatThisMonth - $pejabatLastMonth;
            $pejabatPercent = $pejabatLastMonth > 0 ? round(($pejabatDiff / $pejabatLastMonth) * 100, 1) : ($pejabatThisMonth > 0 ? 100 : 0);

            // Total Users
            $totalUsers = User::excludeSuperDevelopers()->count();
            $usersThisMonth = User::excludeSuperDevelopers()->where('created_at', '>=', $thisMonthStart)->count();
            $usersLastMonth = User::excludeSuperDevelopers()->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
            $usersDiff = $usersThisMonth - $usersLastMonth;
            $usersPercent = $usersLastMonth > 0 ? round(($usersDiff / $usersLastMonth) * 100, 1) : ($usersThisMonth > 0 ? 100 : 0);

            // Total Ruangan & Jenis
            $totalRuangan = \App\Models\Ruangan::count();
            $totalJenis = \App\Models\JenisInventaris::count();

            // Real data: Ruangan assignment
            $assetsWithRoom = (clone $approvedBase)->whereNotNull('ruangan_id')->count();
            $assetsWithoutRoom = $totalAssets - $assetsWithRoom;

            // Real data: Kelengkapan data aset
            $assetsWithNup = (clone $approvedBase)->whereNotNull('nup')->count();
            $assetsWithFoto = \Illuminate\Support\Facades\Schema::hasTable('inventaris_fotos') 
                ? DB::table('inventaris_fotos')->distinct()->count('inventaris_id')
                : 0;
            $assetsWithBarcode = (clone $approvedBase)->whereNotNull('barcode')->count();
            $incompleteAssetsCount = (clone $approvedBase)->kelengkapan('tidak_lengkap')->count();

            // Pejabat berstatus aktif
            $pejabatWithSk = Pejabat::where('is_aktif', 1)->count();
            $totalPejabatAll = Pejabat::count();

            // Real data: Kewajiban Pelaporan
            $latestAssetUpdate = (clone $approvedBase)->max('updated_at');
            $deletedAssetsThisYear = \Illuminate\Support\Facades\Schema::hasTable('inventaris_deleted_log')
                ? DB::table('inventaris_deleted_log')->whereYear('deleted_at', date('Y'))->count()
                : 0;
            $baPenghapusanCount = \Illuminate\Support\Facades\Schema::hasTable('inventaris_deleted_log')
                ? DB::table('inventaris_deleted_log')
                    ->whereYear('deleted_at', date('Y'))
                    ->where(function($q) {
                        $q->whereNotNull('nomor_sk')->orWhereNotNull('dokumen_pdf_path');
                    })->count()
                : 0;

            // Sparkline monthly cumulative data for real trend
            $sparklineAssets = [];
            $sparklineValues = [];
            for ($i = 7; $i >= 0; $i--) {
                $monthObj = now()->subMonths($i);
                $c = (clone $approvedBase)
                    ->where('created_at', '<=', $monthObj->endOfMonth())
                    ->count();
                $v = (clone $approvedBase)
                    ->where('created_at', '<=', $monthObj->endOfMonth())
                    ->sum('nilai_perolehan') ?? 0;
                $sparklineAssets[] = $c;
                $sparklineValues[] = round($v / 1000000000, 2);
            }

            // Kondisi Aset hanya dari inventaris yang sudah disetujui (case-insensitive / multi-format)
            $kondisiBaik        = (clone $approvedBase)->whereIn('kondisi', ['baik', 'Baik', 'BAIK'])->count();
            $kondisiRusakRingan = (clone $approvedBase)->whereIn('kondisi', ['rusak_ringan', 'Rusak Ringan', 'RUSAK RINGAN'])->count();
            $kondisiRusakBerat  = (clone $approvedBase)->whereIn('kondisi', ['rusak_berat', 'Rusak Berat', 'RUSAK BERAT'])->count();

            return [
                'totalAssets' => $totalAssets,
                'totalValue' => $totalValue,
                'totalValueFormatted' => 'Rp ' . number_format($totalValue, fmod($totalValue, 1) != 0 ? 2 : 0, ',', '.'),
                'pejabatActive' => $totalPejabat,
                'usersActive' => $totalUsers,
                'totalRuangan' => $totalRuangan,
                'totalJenis' => $totalJenis,
                'total_inventaris' => $totalAssets,
                'total_nilai' => $totalValue,
                'total_nilai_formatted' => 'Rp ' . number_format($totalValue, 0, ',', '.'),
                'inventaris_diff' => $assetsDiff,
                'pejabat_active' => $totalPejabat,
                'users_active' => $totalUsers,
                'assetsThisMonth' => $assetsThisMonth,
                'assetsWithRoom' => $assetsWithRoom,
                'assetsWithoutRoom' => $assetsWithoutRoom,
                'assetsWithNup' => $assetsWithNup,
                'assetsWithFoto' => $assetsWithFoto,
                'assetsWithBarcode' => $assetsWithBarcode,
                'incompleteAssetsCount' => $incompleteAssetsCount,
                'pejabatWithSk' => $pejabatWithSk,
                'totalPejabatAll' => $totalPejabatAll,
                'latestAssetUpdate' => $latestAssetUpdate,
                'deletedAssetsThisYear' => $deletedAssetsThisYear,
                'baPenghapusanCount' => $baPenghapusanCount,
                'sparklineAssets' => $sparklineAssets,
                'sparklineValues' => $sparklineValues,
                'trends' => [
                    'assets'  => ['diff' => $assetsDiff, 'percent' => $assetsPercent],
                    'value'   => ['diff' => $valueDiff, 'percent' => $valuePercent],
                    'pejabat' => ['diff' => $pejabatDiff, 'percent' => $pejabatPercent],
                    'users'   => ['diff' => $usersDiff, 'percent' => $usersPercent],
                ],
                'kondisiAset' => [
                    'baik'        => $kondisiBaik,
                    'rusakRingan' => $kondisiRusakRingan,
                    'rusakBerat'  => $kondisiRusakBerat,
                ],
                'kondisi' => [
                    'baik'         => $kondisiBaik,
                    'rusak_ringan' => $kondisiRusakRingan,
                    'rusak_berat'  => $kondisiRusakBerat,
                ],
            ];
        });
    }

    private function getChartDataArray(bool $fresh = false)
    {
        if ($fresh) {
            Cache::forget('dashboard_charts');
        }

        return Cache::remember('dashboard_charts', 30, function() {
            $approvedBase = $this->approvedInventarisQuery();

            // Distribusi Nilai Aset per Jenis (Seluruh Jenis Inventaris master, urut dari nilai terbesar)
            $valueByType = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                ->withSum('inventaris as total_nilai', 'nilai_perolehan')
                ->withCount('inventaris')
                ->orderByDesc('total_nilai')
                ->orderByDesc('inventaris_count')
                ->orderBy('nama')
                ->get()
                ->map(function($jenis) {
                    $raw = $jenis->nama ?? 'Lainnya';
                    $clean = preg_replace('/^Inventaris\s+/i', '', $raw);
                    $clean = str_replace(' dan ', ' & ', $clean);
                    if (strcasecmp(trim($clean), 'yang tidak ditemukan') === 0) {
                        $clean = 'Tidak Ditemukan';
                    }
                    return [
                        'id' => $jenis->id,
                        'jenis' => $clean,
                        'jenis_raw' => $raw,
                        'nilai' => (float) ($jenis->total_nilai ?? 0),
                        'count' => (int) ($jenis->inventaris_count ?? 0),
                    ];
                });

            // Komposisi Jumlah Aset per Jenis (Seluruh Jenis Inventaris master)
            $countByType = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                ->withCount('inventaris')
                ->orderByDesc('inventaris_count')
                ->orderBy('nama')
                ->get()
                ->map(function($jenis) {
                    $raw = $jenis->nama ?? 'Lainnya';
                    $clean = preg_replace('/^Inventaris\s+/i', '', $raw);
                    $clean = str_replace(' dan ', ' & ', $clean);
                    if (strcasecmp(trim($clean), 'yang tidak ditemukan') === 0) {
                        $clean = 'Tidak Ditemukan';
                    }
                    return [
                        'id' => $jenis->id,
                        'jenis' => $clean,
                        'jenis_raw' => $raw,
                        'count' => (int) ($jenis->inventaris_count ?? 0),
                    ];
                });

            // Trend Bulanan (12 bulan terakhir)
            $monthlyTrend = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = (clone $approvedBase)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                
                $monthlyTrend[] = [
                    'month' => $month->format('M Y'),
                    'count' => $count,
                ];
            }

            // Distribusi Asal Usul (Kapitalisasi APBDes & Urut Terbesar)
            $asalUsulData = (clone $approvedBase)
                ->select('asal_usul', DB::raw('COUNT(*) as count'))
                ->groupBy('asal_usul')
                ->orderByDesc('count')
                ->get()
                ->map(function($item) {
                    $raw = strtolower($item->asal_usul ?? '');
                    if ($raw === 'apbdes') {
                        $nama = 'APBDes';
                    } elseif ($raw === 'kekayaan_asli' || $raw === 'kekayaan asli') {
                        $nama = 'Kekayaan Asli Desa';
                    } else {
                        $nama = ucwords(str_replace('_', ' ', $item->asal_usul ?? 'Lainnya'));
                    }
                    return [
                        'nama' => $nama,
                        'count' => (int) $item->count,
                    ];
                });

            // Inventaris per Jenis (untuk kartu-kartu)
            $inventarisByJenis = (clone $approvedBase)
                ->select('jenis_inventaris_id', DB::raw('COUNT(*) as count'))
                ->with('jenis:id,nama')
                ->groupBy('jenis_inventaris_id')
                ->get()
                ->mapWithKeys(function($item) {
                    $jenisNama = $item->jenis->nama ?? 'unknown';
                    return [$jenisNama => $item->count];
                });

            // Total untuk main card
            $totalInv = $inventarisByJenis->sum();

            return [
                'labels' => $valueByType->pluck('jenis')->toArray(),
                'labelsJson' => json_encode($valueByType->pluck('jenis')->toArray()),
                'valueByType' => $valueByType->pluck('nilai')->toArray(),
                'valueByTypeJson' => json_encode($valueByType->pluck('nilai')->toArray()),
                'countByType' => $countByType->pluck('count')->toArray(),
                'countByTypeJson' => json_encode($countByType->pluck('count')->toArray()),
                'monthlyTrend' => $monthlyTrend,
                'monthlyTrendJson' => json_encode($monthlyTrend),
                'asalUsul' => $asalUsulData->toArray(),
                'inventarisByJenis' => $inventarisByJenis,
                'totalInventaris' => $totalInv,
                'jumlah_per_jenis' => [
                    'labels' => $countByType->pluck('jenis')->toArray(),
                    'data' => $countByType->pluck('count')->toArray(),
                ],
                'nilai_per_jenis' => [
                    'labels' => $valueByType->pluck('jenis')->toArray(),
                    'data' => $valueByType->pluck('nilai')->toArray(),
                ],
            ];
        });
    }

    /**
     * Get recent activities from activity_logs table (for view rendering)
     */
    private function getRecentActivitiesArray()
    {
        $colors = [
            'tambah'  => '#10b981',
            'ubah'    => '#f59e0b',
            'update'  => '#f59e0b',
            'hapus'   => '#ef4444',
            'approve' => '#059669',
            'reject'  => '#dc2626',
            'export'  => '#f59e0b',
        ];

        return ActivityLog::excludeSuperDevelopers()
            ->with('user:id,nama')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) use ($colors) {
                return [
                    'text'  => ($log->user->nama ?? 'User') . ' ' . lcfirst($log->detail),
                    'time'  => $log->created_at ? $log->created_at->diffForHumans() : '-',
                    'color' => $colors[$log->action] ?? '#6b7280',
                ];
            })
            ->toArray();
    }

    /**
     * Get pending approvals as array (for view rendering)
     * REMOVED — approval workflow disabled
     *
     * Get recent exports as array (for view rendering)
     * 
     * Data source:
     * - Real jenis: dari jenisInventaris relationship (jika jenis_inventaris_id != null)
     * - Virtual jenis: dari filters['virtual_export_as'] (jika jenis_inventaris_id = null)
     */

    private function getRecentExportsArray()
    {
        return ExportLog::excludeSuperDevelopers()
            ->with(['user:id,nama', 'jenisInventaris:id,nama'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'user' => $item->user->nama ?? 'Unknown',
                    'jenis' => $item->jenis_label,
                    'tanggal' => $item->created_at ? $item->created_at->format('d M Y, H:i') : '-',
                ];
            });
    }

    /**
     * API endpoint untuk fetch KPI data
     * GET /dashboard-api/kpi
     */
    public function getKpiData(Request $request)
    {
        $fresh = $request->boolean('fresh', false);
        return response()->json($this->getKpiDataArray($fresh));
    }

    /**
     * API endpoint untuk fetch chart data
     * GET /dashboard-api/charts
     */
    public function getChartData(Request $request)
    {
        if ($request->boolean('fresh', false)) {
            Cache::forget('dashboard_charts');
        }
        return response()->json($this->buildDashboardChartsPayload($request));
    }

    public function exportChartPdf(Request $request, string $chart)
    {
        $this->authorize('viewAny', ExportLog::class);

        $chart = strtolower($chart);
        abort_unless(in_array($chart, ['bar', 'donut', 'line'], true), 404);

        $payload = $this->buildDashboardChartsPayload($request);
        $meta = $this->buildChartExportMeta($chart, $request, $payload);

        $html = view('dashboard.exports.chart-pdf', [
            'chartType' => $chart,
            'title' => $meta['title'],
            'subtitle' => $meta['subtitle'],
            'filters' => $meta['filters'],
            'rows' => $meta['rows'],
            'metricLabel' => $meta['metricLabel'],
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
            'generatedBy' => optional($request->user())->nama ?? 'User',
            'totals' => $meta['totals'],
        ])->render();

        $pdfBinary = $this->renderPdfBinaryFromHtml($html);

        $filename = 'dashboard-chart-' . $chart . '_' . now()->format('Ymd_His') . '.pdf';
        $relativePath = 'exports/' . now()->format('Y/m') . '/' . $filename;
        Storage::disk('local')->put($relativePath, $pdfBinary);

        ExportLog::create([
            'user_id' => $request->user()?->id ?? \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id,
            'inventaris_id' => null,
            'jenis_inventaris_id' => null,
            'pejabat_id' => null,
            'tipe_export' => 'pdf',
            'nama_file' => $filename,
            'pejabat_override' => null,
            'filters' => [
                'dashboard_chart' => $chart,
                'dashboard_chart_title' => $meta['title'],
                'dashboard_filters' => $meta['filters'],
                'virtual_export_as' => 'Dashboard - ' . $meta['title'],
            ],
        ]);

        return response()->download(storage_path('app/' . $relativePath), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function buildDashboardChartsPayload(Request $request): array
    {
        // Bar chart filters
        $barTahun    = $request->input('bar_tahun');
        $barKondisi  = $request->input('bar_kondisi');
        $barMetric   = $request->input('bar_metric', 'nilai');

        // Donut chart filters
        $donutTahun   = $request->input('donut_tahun');
        $donutKondisi = $request->input('donut_kondisi');
        $donutMetric  = $request->input('donut_metric', 'jumlah');

        // Line chart filters
        $rentang      = in_array((int) $request->input('rentang', 6), [6, 12])
                          ? (int) $request->input('rentang', 6)
                          : 6;
        $defaultSemester = now()->month > 6 ? 2 : 1;
        $semester     = $request->has('semester') && in_array((int) $request->input('semester'), [1, 2], true)
                          ? (int) $request->input('semester')
                          : $defaultSemester;
        $jenisId      = $request->input('jenis_id');
        $kondisiTrend = $request->input('kondisi_trend');
        $trendTahunInput = $request->input('trend_tahun');
        $trendTahun   = ($trendTahunInput && $trendTahunInput !== 'all') ? (int) $trendTahunInput : (int) now()->year;

        $barQuery = $this->approvedInventarisQuery()->with('jenis:id,nama,icon_mode,custom_color');
        if ($barTahun) {
            $barQuery->whereYear('created_at', $barTahun);
        }
        if ($barKondisi) {
            $barQuery->where('kondisi', $barKondisi);
        }

        if ($barMetric === 'jumlah') {
            $barResult = $barQuery->select('jenis_inventaris_id', DB::raw('COUNT(*) as total'))
                ->groupBy('jenis_inventaris_id')->get()
                ->map(fn($i) => [
                    'jenis' => $i->jenis->nama ?? 'Unknown',
                    'value' => (int) $i->total,
                    'color' => $i->jenis->color_gradient[0] ?? '#519259'
                ]);
        } else {
            $barResult = $barQuery->select('jenis_inventaris_id', DB::raw('SUM(nilai_perolehan) as total'))
                ->groupBy('jenis_inventaris_id')->get()
                ->map(fn($i) => [
                    'jenis' => $i->jenis->nama ?? 'Unknown',
                    'value' => (float) $i->total,
                    'color' => $i->jenis->color_gradient[0] ?? '#519259'
                ]);
        }

        $donutQuery = $this->approvedInventarisQuery()->with('jenis:id,nama,icon_mode,custom_color');
        if ($donutTahun) {
            $donutQuery->whereYear('created_at', $donutTahun);
        }
        if ($donutKondisi) {
            $donutQuery->where('kondisi', $donutKondisi);
        }

        if ($donutMetric === 'nilai') {
            $donutResult = $donutQuery->select('jenis_inventaris_id', DB::raw('SUM(nilai_perolehan) as total'))
                ->groupBy('jenis_inventaris_id')->get()
                ->map(fn($i) => [
                    'jenis' => $i->jenis->nama ?? 'Unknown',
                    'value' => (float) $i->total,
                    'color' => $i->jenis->color_gradient[0] ?? '#519259'
                ]);
        } else {
            $donutResult = $donutQuery->select('jenis_inventaris_id', DB::raw('COUNT(*) as total'))
                ->groupBy('jenis_inventaris_id')->get()
                ->map(fn($i) => [
                    'jenis' => $i->jenis->nama ?? 'Unknown',
                    'value' => (int) $i->total,
                    'color' => $i->jenis->color_gradient[0] ?? '#519259'
                ]);
        }

        // Trend Bulanan: hitung range bulan berdasarkan rentang & semester
        // - rentang=6, semester=1 → Jan–Jun
        // - rentang=6, semester=2 → Jul–Des
        // - rentang=12            → Jan–Des (atau s/d bulan sekarang jika tahun ini)
        $monthlyTrend  = [];
        $isCurrentYear = ($trendTahun === (int) now()->year);

        if ($rentang === 6) {
            $startMonth = ($semester === 2) ? 7 : 1;
            $endMonth   = ($semester === 2) ? 12 : 6;
            // Range semester selalu penuh — bulan yang belum terjadi tampil sebagai 0
        } else {
            // 1 Tahun
            $startMonth = 1;
            $endMonth   = $isCurrentYear ? now()->month : 12;
        }

        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $monthLabel = \Carbon\Carbon::createFromDate($trendTahun, $m, 1)->format('M Y');

            // Hitung jumlah inventaris yang ditambahkan pada tahun & bulan tersebut
            $trendQ = $this->approvedInventarisQuery()
                ->whereYear('created_at', $trendTahun)
                ->whereMonth('created_at', sprintf('%02d', $m));

            if ($jenisId) {
                $trendQ->where('jenis_inventaris_id', $jenisId);
            }
            if ($kondisiTrend) {
                $trendQ->where('kondisi', $kondisiTrend);
            }

            $monthlyTrend[] = [
                'month' => $monthLabel,
                'count' => $trendQ->count(),
            ];
        }

        $inventarisByJenis = $this->approvedInventarisQuery()
            ->select('jenis_inventaris_id', DB::raw('COUNT(*) as count'))
            ->with('jenis:id,nama')->groupBy('jenis_inventaris_id')->get()
            ->mapWithKeys(fn($i) => [strtolower(str_replace(' ', '_', $i->jenis->nama ?? 'unknown')) => $i->count]);

        $currentYear = now()->year;
        $availableYears = range($currentYear, $currentYear - 4);
        $availableJenis = JenisInventaris::select('id', 'nama')
            ->where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('nama')
            ->get();

        // Nilai perolehan per jenis inventaris (fleksibel, mengikuti jenis yang ada di DB)
        $nilaiInventaris = $this->approvedInventarisQuery()
            ->select('jenis_inventaris_id', DB::raw('SUM(nilai_perolehan) as total_nilai'))
            ->with('jenis:id,nama')
            ->groupBy('jenis_inventaris_id')
            ->get()
            ->map(fn($i) => [
                'id'    => $i->jenis_inventaris_id,
                'nama'  => $i->jenis->nama ?? 'Unknown',
                'nilai' => (float) ($i->total_nilai ?? 0),
            ])
            ->values()
            ->toArray();

        // Kondisi per jenis inventaris (fleksibel, mengikuti jenis yang ada di DB)
        $inventarisKondisi = $this->approvedInventarisQuery()
            ->select(
                'jenis_inventaris_id',
                DB::raw("SUM(CASE WHEN kondisi = 'baik' THEN 1 ELSE 0 END) as baik"),
                DB::raw("SUM(CASE WHEN kondisi = 'rusak_ringan' THEN 1 ELSE 0 END) as rusak_ringan"),
                DB::raw("SUM(CASE WHEN kondisi = 'rusak_berat' THEN 1 ELSE 0 END) as rusak_berat")
            )
            ->with('jenis:id,nama')
            ->groupBy('jenis_inventaris_id')
            ->get()
            ->map(fn($i) => [
                'id'          => $i->jenis_inventaris_id,
                'nama'        => $i->jenis->nama ?? 'Unknown',
                'baik'        => (int) ($i->baik ?? 0),
                'rusakRingan' => (int) ($i->rusak_ringan ?? 0),
                'rusakBerat'  => (int) ($i->rusak_berat ?? 0),
            ])
            ->values()
            ->toArray();

        return [
            'barLabels'         => $barResult->pluck('jenis')->toArray(),
            'barValues'         => $barResult->pluck('value')->toArray(),
            'barColors'         => $barResult->pluck('color')->toArray(),
            'donutLabels'       => $donutResult->pluck('jenis')->toArray(),
            'donutValues'       => $donutResult->pluck('value')->toArray(),
            'donutColors'       => $donutResult->pluck('color')->toArray(),
            'monthlyTrend'      => $monthlyTrend,
            'inventarisByJenis' => $inventarisByJenis,
            'availableYears'    => $availableYears,
            'availableJenis'    => $availableJenis,
            'barMetric'         => $barMetric,
            'donutMetric'       => $donutMetric,
            'nilaiInventaris'   => $nilaiInventaris,
            'inventarisKondisi' => $inventarisKondisi,
            'trendTahun'        => $trendTahun,
        ];
    }

    private function buildChartExportMeta(string $chart, Request $request, array $payload): array
    {
        if ($chart === 'bar') {
            $rows = [];
            $labels = $payload['barLabels'] ?? [];
            $values = $payload['barValues'] ?? [];
            foreach ($labels as $idx => $label) {
                $rows[] = [
                    'label' => $label,
                    'value' => (float) ($values[$idx] ?? 0),
                ];
            }

            return [
                'title' => 'Distribusi Nilai Aset per Jenis',
                'subtitle' => 'Ringkasan distribusi nilai atau jumlah aset per jenis',
                'metricLabel' => $request->input('bar_metric', 'nilai') === 'jumlah' ? 'Jumlah Unit' : 'Nilai Perolehan',
                'filters' => [
                    'Metrik' => $request->input('bar_metric', 'nilai') === 'jumlah' ? 'Jumlah Unit' : 'Nilai Perolehan',
                    'Tahun Input' => $request->input('bar_tahun', '') !== '' ? $request->input('bar_tahun') : 'Semua Tahun',
                    'Kondisi' => $request->input('bar_kondisi', '') !== '' ? $request->input('bar_kondisi') : 'Semua Kondisi',
                ],
                'rows' => $rows,
                'totals' => [
                    'Jumlah Kategori' => count($rows),
                    'Total Nilai' => array_sum(array_map(fn($r) => (float) $r['value'], $rows)),
                ],
            ];
        }

        if ($chart === 'donut') {
            $rows = [];
            $labels = $payload['donutLabels'] ?? [];
            $values = $payload['donutValues'] ?? [];
            foreach ($labels as $idx => $label) {
                $rows[] = [
                    'label' => $label,
                    'value' => (float) ($values[$idx] ?? 0),
                ];
            }

            return [
                'title' => 'Komposisi Aset',
                'subtitle' => 'Ringkasan komposisi aset per jenis',
                'metricLabel' => $request->input('donut_metric', 'jumlah') === 'nilai' ? 'Total Nilai' : 'Jumlah Unit',
                'filters' => [
                    'Metrik' => $request->input('donut_metric', 'jumlah') === 'nilai' ? 'Total Nilai' : 'Jumlah Unit',
                    'Tahun Input' => $request->input('donut_tahun', '') !== '' ? $request->input('donut_tahun') : 'Semua Tahun',
                    'Kondisi' => $request->input('donut_kondisi', '') !== '' ? $request->input('donut_kondisi') : 'Semua Kondisi',
                ],
                'rows' => $rows,
                'totals' => [
                    'Jumlah Kategori' => count($rows),
                    'Total Nilai' => array_sum(array_map(fn($r) => (float) $r['value'], $rows)),
                ],
            ];
        }

        $jenisId = $request->input('jenis_id');
        $jenisNama = 'Semua Jenis';
        if ($jenisId) {
            $jenisNama = JenisInventaris::where('id', $jenisId)->value('nama') ?? 'Jenis tidak ditemukan';
        }

        $trendRows = array_map(function ($row) {
            return [
                'label' => $row['month'] ?? '-',
                'value' => (float) ($row['count'] ?? 0),
            ];
        }, $payload['monthlyTrend'] ?? []);

        $rentangExport = in_array((int) $request->input('rentang', 6), [6, 12])
                           ? (int) $request->input('rentang', 6)
                           : 6;

        $semesterLabel = '';
        if ($rentangExport === 6) {
            $semesterVal  = in_array((int) $request->input('semester', 1), [1, 2])
                              ? (int) $request->input('semester', 1) : 1;
            $semesterLabel = $semesterVal === 2 ? 'Semester II (Jul–Des)' : 'Semester I (Jan–Jun)';
        }

        return [
            'title' => 'Trend Bulanan',
            'subtitle' => 'Ringkasan tren penambahan inventaris per bulan',
            'metricLabel' => 'Jumlah Aset',
            'filters' => [
                'Rentang Bulan' => $rentangExport === 6
                    ? '6 Bulan – ' . $semesterLabel
                    : '1 Tahun',
                'Tahun Perolehan' => (string) $request->input('trend_tahun', now()->year),
                'Jenis' => $jenisNama,
                'Kondisi' => $request->input('kondisi_trend', '') !== '' ? $request->input('kondisi_trend') : 'Semua Kondisi',
            ],
            'rows' => $trendRows,
            'totals' => [
                'Jumlah Periode' => count($trendRows),
                'Total Nilai' => array_sum(array_map(fn($r) => (float) $r['value'], $trendRows)),
            ],
        ];
    }

    private function renderPdfBinaryFromHtml(string $html): string
    {
        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->output();
        }

        if (function_exists('app') && app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->output();
        }

        if (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return $dompdf->output();
        }

        abort(500, 'PDF engine not available.');
    }

    /**
     * API endpoint untuk fetch recent activities
     * GET /api/dashboard/activities
     */
    public function getRecentActivities()
    {
        // Allow authenticated users to fetch recent activities.
        // (Previous code incorrectly authorized against ExportLog.)

        $colors = [
            'tambah'  => '#10b981',
            'ubah'    => '#f59e0b',
            'update'  => '#f59e0b',
            'hapus'   => '#ef4444',
            'approve' => '#059669',
            'reject'  => '#dc2626',
            'export'  => '#f59e0b',
        ];

        $activities = ActivityLog::excludeSuperDevelopers()
            ->with('user:id,nama')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) use ($colors) {
                return [
                    'text'  => ($log->user->nama ?? 'User') . ' ' . $log->detail,
                    'time'  => $log->created_at ? $log->created_at->diffForHumans() : '-',
                    'color' => $colors[$log->action] ?? '#6b7280',
                ];
            });

        return response()->json($activities);
    }

    // =========================================================
    // HALAMAN LOG LENGKAP (page baru, bukan API)
    // =========================================================

    /**
     * Halaman semua aktivitas terbaru (paginated)
     */
    public function allActivities(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        /** @var \App\Models\User|null $user */
        $user = $request->user();
        $canDelete = $user && ($user->isSuperAdmin() || $user->isSuperDeveloper());

        $colors = [
            'tambah'  => '#10b981',
            'ubah'    => '#f59e0b',
            'update'  => '#f59e0b',
            'hapus'   => '#ef4444',
            'export'  => '#3b82f6',
        ];

        $query = ActivityLog::excludeSuperDevelopers()
            ->with(['user.role', 'user.bidang', 'user.dinas']);

        $sortBy = $request->input('sort', 'terbaru');
        if ($sortBy === 'nama_asc') {
            $query->orderBy('subject_name', 'asc');
        } elseif ($sortBy === 'nama_desc') {
            $query->orderBy('subject_name', 'desc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if ($request->filled('action')) {
            $actionFilter = strtolower(trim((string) $request->action));
            if (in_array($actionFilter, ['ubah', 'update'])) {
                $query->whereIn('action', ['ubah', 'update']);
            } else {
                $query->where('action', $request->action);
            }
        }
        if ($request->filled('module')) {
            $mod = $request->module;
            $modMapping = [
                'Inventaris'      => ['Inventaris', 'inventaris', 'buku-inventaris', 'buku_inventaris', 'ManajemenAset', 'manajemen-aset', 'manajemen_aset'],
                'PenghapusanAset' => ['PenghapusanAset', 'penghapusan', 'penghapusan-aset', 'penghapusan_aset', 'Penghapusan', 'Penghapusan Aset'],
                'JenisInventaris' => ['JenisInventaris', 'jenisinventaris', 'jenis_inventaris', 'jenis-inventaris', 'Jenis Inventaris'],
                'User'            => ['User', 'user', 'users', 'Users', 'manajemen-pengguna'],
                'Ruangan'         => ['Ruangan', 'ruangan', 'ruangans'],
                'Bidang'          => ['Bidang', 'bidang', 'bidangs'],
                'Pejabat'         => ['Pejabat', 'pejabat', 'pejabats', 'manajemen-pejabat'],
                'Dropdown'        => ['Dropdown', 'dropdown', 'dropdowns', 'dropdown-dinamis'],
                'Export'          => ['Export', 'export', 'ExportData', 'export-data', 'export_data', 'pusat-ekspor'],
                'Backup'          => ['Backup', 'backup', 'cadangan', 'Cadangan'],
                'Profile'         => ['Profile', 'profile', 'profil', 'auth'],
                'Aktivitas'       => ['Aktivitas', 'aktivitas', 'aktivitas-sistem', 'activity', 'activity_log'],
            ];

            if (isset($modMapping[$mod])) {
                $query->whereIn('module', $modMapping[$mod]);
            } else {
                $query->where('module', $mod);
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('detail', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', '%' . $search . '%'));
            });
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $logs */
        $logs = $query->paginate($perPage)->withQueryString();

        // Group by date label (Hari ini / Kemarin / tanggal)
        $grouped = $logs->getCollection()->groupBy(function ($log) {
            if (!$log->created_at) return 'Lainnya';
            if ($log->created_at->isToday()) return 'Hari ini';
            if ($log->created_at->isYesterday()) return 'Kemarin';
            return $log->created_at->translatedFormat('d F Y');
        });

        $actions  = ActivityLog::excludeSuperDevelopers()->distinct()->orderBy('action')->pluck('action');
        $modules  = ActivityLog::excludeSuperDevelopers()->distinct()->orderBy('module')->pluck('module');

        return view('dashboard.activities', compact('logs', 'grouped', 'colors', 'actions', 'modules', 'canDelete'));
    }

    /**
     * Ekspor log aktivitas sistem ke Excel atau PDF.
     */
    public function exportActivities(Request $request)
    {
        $this->abortIfNotSuperAdmin($request);
        $format = strtolower($request->query('format', 'excel'));

        $query = ActivityLog::excludeSuperDevelopers()
            ->with(['user.role', 'user.bidang', 'user.dinas']);

        if ($request->filled('action')) {
            $actionFilter = strtolower(trim((string) $request->action));
            if (in_array($actionFilter, ['ubah', 'update'])) {
                $query->whereIn('action', ['ubah', 'update']);
            } else {
                $query->where('action', $request->action);
            }
        }

        if ($request->filled('module')) {
            $mod = $request->module;
            $modMapping = [
                'Inventaris'      => ['Inventaris', 'inventaris', 'buku-inventaris', 'buku_inventaris', 'ManajemenAset', 'manajemen-aset', 'manajemen_aset'],
                'PenghapusanAset' => ['PenghapusanAset', 'penghapusan', 'penghapusan-aset', 'penghapusan_aset', 'Penghapusan', 'Penghapusan Aset'],
                'JenisInventaris' => ['JenisInventaris', 'jenisinventaris', 'jenis_inventaris', 'jenis-inventaris', 'Jenis Inventaris'],
                'User'            => ['User', 'user', 'users', 'Users', 'manajemen-pengguna'],
                'Ruangan'         => ['Ruangan', 'ruangan', 'ruangans'],
                'Bidang'          => ['Bidang', 'bidang', 'bidangs'],
                'Pejabat'         => ['Pejabat', 'pejabat', 'pejabats', 'manajemen-pejabat'],
                'Dropdown'        => ['Dropdown', 'dropdown', 'dropdowns', 'dropdown-dinamis'],
                'Export'          => ['Export', 'export', 'ExportData', 'export-data', 'export_data', 'pusat-ekspor'],
                'Backup'          => ['Backup', 'backup', 'cadangan', 'Cadangan'],
                'Profile'         => ['Profile', 'profile', 'profil', 'auth'],
                'Aktivitas'       => ['Aktivitas', 'aktivitas', 'aktivitas-sistem', 'activity', 'activity_log'],
            ];

            if (isset($modMapping[$mod])) {
                $query->whereIn('module', $modMapping[$mod]);
            } else {
                $query->where('module', $mod);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('detail', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', '%' . $search . '%'));
            });
        }

        $dateStr = date('Ymd_His');

        \App\Services\ActivityLogger::log('Aktivitas Sistem', 'export', 'Mengekspor log audit aktivitas sistem (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $totalCount = (clone $query)->count();
            $maxPdfLimit = 1000;
            $isCapped = $totalCount > $maxPdfLimit;
            $logs = $query->orderByDesc('created_at')->limit($maxPdfLimit)->get();

            $headers = [
                ['text' => 'NO', 'width' => '4%', 'align' => 'center'],
                ['text' => 'WAKTU & TANGGAL', 'width' => '15%', 'align' => 'center'],
                ['text' => 'PENGGUNA', 'width' => '15%', 'align' => 'left'],
                ['text' => 'PERAN / JABATAN', 'width' => '15%', 'align' => 'left'],
                ['text' => 'MODUL', 'width' => '11%', 'align' => 'left'],
                ['text' => 'AKTIVITAS', 'width' => '12%', 'align' => 'center'],
                ['text' => 'RINCIAN DETAIL', 'width' => '28%', 'align' => 'left'],
            ];

            $rows = [];
            foreach ($logs as $i => $item) {
                $userNama = $item->user?->nama ?? 'Sistem';
                $userRole = $item->user?->role_name_display ?: ($item->user ? ($item->user->role?->nama ? ucwords(str_replace('_', ' ', $item->user->role->nama)) : '-') : 'Sistem');
                $module   = $item->module_label ?? ucfirst((string)$item->module);
                $action   = $item->action_label ?? (ucfirst((string)$item->action) . ' Data');

                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $item->created_at ? $item->created_at->translatedFormat('d/m/Y H:i:s') : '-', 'class' => 'text-center'],
                    ['text' => $userNama],
                    ['text' => $userRole],
                    ['text' => $module],
                    ['text' => $action, 'class' => 'text-center'],
                    ['text' => $item->detail ?? '-'],
                ];
            }

            $summary = [
                'Total Aktivitas' => $isCapped
                    ? number_format(count($logs), 0, ',', '.') . ' dari ' . number_format($totalCount, 0, ',', '.') . ' (1.000 Terkini)'
                    : count($logs) . ' Rekaman',
            ];

            $subtitle = $isCapped
                ? 'Rekapitulasi Catatan Aktivitas dan Keamanan Pengguna Sistem (Dibatasi 1.000 data terkini - Gunakan format Excel untuk arsip lengkap)'
                : 'Rekapitulasi Catatan Aktivitas dan Keamanan Pengguna Sistem';

            return \App\Services\FeatureExportService::downloadPdf(
                'Laporan Audit Trail Aktivitas Sistem',
                $subtitle,
                $headers,
                $rows,
                "Log_Aktivitas_{$dateStr}.pdf",
                'landscape',
                $summary
            );
        }

        $logs = $query->orderByDesc('created_at')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActivityLogExport($logs),
            "Log_Aktivitas_{$dateStr}.xlsx"
        );
    }

    public function destroyActivity(Request $request, ActivityLog $activityLog)
    {
        $this->abortIfNotSuperAdmin($request);

        $activityLog->delete();

        return redirect()->back()->with('success', 'Data aktivitas berhasil dihapus.');
    }

    public function destroyAllActivities(Request $request)
    {
        $this->abortIfNotSuperAdmin($request);

        $deleted = ActivityLog::query()->delete();
        $message = $deleted > 0
            ? 'Semua data aktivitas berhasil dihapus.'
            : 'Tidak ada data aktivitas yang perlu dihapus.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Halaman semua export log (paginated)
     */
    public function allExports(Request $request)
    {
        // Redirect dashboard-level "all exports" page to the main export history view
        // Preserve any query parameters and mark origin so history can provide proper back navigation.
        $query = array_merge($request->query(), ['from' => 'dashboard']);
        return redirect()->route('export-data.history', $query);
    }

    /**
     * Halaman semua inventaris menunggu persetujuan (paginated)
     */
    public function allPending(Request $request)
    {
        // DISABLED: persetujuan page removed — redirect to dashboard
        return redirect()->route('dashboard')->with('error', 'Fitur persetujuan telah dinonaktifkan.');
    }

    /**
     * API endpoint untuk fetch recent export logs
     * GET /api/dashboard/exports
     * 
     * Fetch jenis dari:
     * 1. Jika jenis_inventaris_id ada → relasi jenisInventaris (real jenis dari DB)
     * 2. Jika jenis_inventaris_id = null → lookup filters['virtual_export_as'] (virtual jenis dari sistem)
     * 3. Jika tidak ada keduanya → fallback "Unknown"
     */
    public function getRecentExports()
    {
        $this->authorize('viewAny', ExportLog::class);

        $exports = ExportLog::excludeSuperDevelopers()
            ->with(['user:id,nama', 'jenisInventaris:id,nama'])
            ->whereIn('tipe_export', ['excel', 'pdf'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'user' => $item->user->nama ?? 'Unknown',
                    'jenis' => $item->jenis_label,
                    'tanggal' => $item->created_at ? $item->created_at->format('d M Y, H:i') : '-',
                    'tipe' => strtoupper($item->tipe_export),
                ];
            });

        return response()->json($exports);
    }

    private function abortIfNotSuperAdmin(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        abort_unless($user && ($user->isSuperAdmin() || $user->isSuperDeveloper()), 403);
    }
}
