<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pejabat;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
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
        $kpis = $this->getKpiDataArray();
        $chartData = $this->getChartDataArray();
        $activities = $this->getRecentActivitiesArray();
        $exportLogs = $this->getRecentExportsArray();
        $jenisInventarisList = $this->getJenisInventarisListArray();

        return view('dashboard.index', compact('kpis', 'chartData', 'activities', 'exportLogs', 'jenisInventarisList'));
    }

    /**
     * Ambil semua jenis inventaris beserta jumlah inventarisnya (dinamis)
     */
    private function getJenisInventarisListArray()
    {
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
    }

    /**
     * Query dasar inventaris yang sudah disetujui untuk kebutuhan dashboard.
     */
    private function approvedInventarisQuery()
    {
        // Approval workflow is disabled; use all inventaris records for dashboard metrics
        return Inventaris::query();
    }

    /**
     * Get KPI data as array (for view rendering)
     */
    private function getKpiDataArray()
    {
        return Cache::remember('dashboard_kpi', 300, function() {
            $approvedBase = $this->approvedInventarisQuery();

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

            // Kondisi Aset hanya dari inventaris yang sudah disetujui
            $kondisiBaik        = (clone $approvedBase)->where('kondisi', 'baik')->count();
            $kondisiRusakRingan = (clone $approvedBase)->where('kondisi', 'rusak_ringan')->count();
            $kondisiRusakBerat  = (clone $approvedBase)->where('kondisi', 'rusak_berat')->count();

            return [
                'totalAssets' => $totalAssets,
                'totalValue' => $totalValue,
                'totalValueFormatted' => 'Rp ' . number_format($totalValue, 0, ',', '.'),
                'pejabatActive' => $totalPejabat,
                'usersActive' => $totalUsers,
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
            ];
        });
    }

    /**
     * Get chart data as array (for view rendering)
     */
    private function getChartDataArray()
    {
        return Cache::remember('dashboard_charts', 600, function() {
            $approvedBase = $this->approvedInventarisQuery();

            // Distribusi Nilai Aset per Jenis
            $valueByType = (clone $approvedBase)
                ->select('jenis_inventaris_id', DB::raw('SUM(nilai_perolehan) as total_nilai'))
                ->with('jenis:id,nama')
                ->groupBy('jenis_inventaris_id')
                ->get()
                ->map(function($item) {
                    return [
                        'jenis' => $item->jenis->nama ?? 'Unknown',
                        'nilai' => (float) $item->total_nilai,
                    ];
                });

            // Komposisi Jumlah Aset per Jenis
            $countByType = (clone $approvedBase)
                ->select('jenis_inventaris_id', DB::raw('COUNT(*) as count'))
                ->with('jenis:id,nama')
                ->groupBy('jenis_inventaris_id')
                ->get()
                ->map(function($item) {
                    return [
                        'jenis' => $item->jenis->nama ?? 'Unknown',
                        'count' => $item->count,
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
                'labels' => $countByType->pluck('jenis')->toArray(),
                'labelsJson' => json_encode($countByType->pluck('jenis')->toArray()),
                'valueByType' => $valueByType->pluck('nilai')->toArray(),
                'valueByTypeJson' => json_encode($valueByType->pluck('nilai')->toArray()),
                'countByType' => $countByType->pluck('count')->toArray(),
                'countByTypeJson' => json_encode($countByType->pluck('count')->toArray()),
                'monthlyTrend' => $monthlyTrend,
                'monthlyTrendJson' => json_encode($monthlyTrend),
                'inventarisByJenis' => $inventarisByJenis,
                'totalInventaris' => $totalInv,
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
            'hapus'   => '#ef4444',
            'approve' => '#059669',
            'reject'  => '#dc2626',
            'export'  => '#f59e0b',
        ];

        return ActivityLog::with('user:id,nama')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) use ($colors) {
                return [
                    'text'  => ($log->user->nama ?? 'User') . ' ' . $log->detail,
                    'time'  => $log->created_at->diffForHumans(),
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
        return ExportLog::with(['user:id,nama', 'jenisInventaris:id,nama'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Determine jenis: real or virtual
                if ($item->jenis_inventaris_id) {
                    $jenis = $item->jenisInventaris->nama ?? 'Unknown';
                } else {
                    $filters = is_array($item->filters) ? $item->filters : [];
                    $jenis = $filters['virtual_export_as'] ?? 'Semua Jenis';
                }
                
                return [
                    'user' => $item->user->nama ?? 'Unknown',
                    'jenis' => $jenis,
                    'tanggal' => $item->created_at ? $item->created_at->format('d M Y, H:i') : '-',
                ];
            });
    }

    /**
     * API endpoint untuk fetch KPI data
     * GET /api/dashboard/kpi
     */
    public function getKpiData()
    {
        $kpis = Cache::remember('dashboard_kpi', 300, function() {
            $base = $this->approvedInventarisQuery();

            $totalAssets        = (clone $base)->count();
            $totalValue         = (clone $base)->sum('nilai_perolehan') ?? 0;
            $totalPejabat       = Pejabat::where('is_aktif', 1)->count();
            $totalUsers         = User::count();
            $kondisiBaik        = (clone $base)->where('kondisi', 'baik')->count();
            $kondisiRusakRingan = (clone $base)->where('kondisi', 'rusak_ringan')->count();
            $kondisiRusakBerat  = (clone $base)->where('kondisi', 'rusak_berat')->count();

            return [
                'totalAssets'  => $totalAssets,
                'totalValue'   => $totalValue,
                'pejabatActive'=> $totalPejabat,
                'usersActive'  => $totalUsers,
                'kondisiAset'  => [
                    'baik'        => $kondisiBaik,
                    'rusakRingan' => $kondisiRusakRingan,
                    'rusakBerat'  => $kondisiRusakBerat,
                ],
            ];
        });

        return response()->json($kpis);
    }

    /**
     * API endpoint untuk fetch chart data
     * GET /api/dashboard/charts
     */
    public function getChartData(Request $request)
    {
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
            'user_id' => (int) optional($request->user())->id,
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
        $trendTahun   = (int) $request->input('trend_tahun', now()->year);

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

            // Filter tahun dari tahun_perolehan, bulan dari created_at
            // (tahun_perolehan hanya menyimpan tahun, bukan tanggal lengkap)
            $trendQ = $this->approvedInventarisQuery()
                ->where('tahun_perolehan', $trendTahun)
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
            'hapus'   => '#ef4444',
            'approve' => '#059669',
            'reject'  => '#dc2626',
            'export'  => '#f59e0b',
        ];

        $activities = ActivityLog::with('user:id,nama')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) use ($colors) {
                return [
                    'text'  => ($log->user->nama ?? 'User') . ' ' . $log->detail,
                    'time'  => $log->created_at->diffForHumans(),
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
        $canDelete = $user && $user->isSuperAdmin();

        $colors = [
            'tambah'  => '#10b981',
            'ubah'    => '#f59e0b',
            'hapus'   => '#ef4444',
            'export'  => '#3b82f6',
        ];

        $query = ActivityLog::with('user:id,nama');

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
            $query->where('action', $request->action);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
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

        $actions  = ActivityLog::distinct()->orderBy('action')->pluck('action');
        $modules  = ActivityLog::distinct()->orderBy('module')->pluck('module');

        if ($request->ajax()) {
            return response()->json([
                'logs' => $logs->getCollection()->map(function($log) use ($colors, $canDelete) {
                    return [
                        'id' => $log->id,
                        'user_name' => $log->user?->nama ?? 'Sistem',
                        'badge_bg' => $colors[$log->action] ?? '#6b7280',
                        'action_label' => ucfirst($log->action),
                        'detail' => $log->detail,
                        'created_at_formatted' => $log->created_at?->translatedFormat('d M Y, H:i'),
                        'destroy_url' => route('dashboard.activities.destroy', $log->id),
                    ];
                }),
                'pagination' => [
                    'firstItem' => $logs->firstItem() ?? 0,
                    'lastItem' => $logs->lastItem() ?? 0,
                    'total' => $logs->total(),
                    'currentPage' => $logs->currentPage(),
                    'lastPage' => $logs->lastPage(),
                    'hasPages' => $logs->hasPages(),
                ]
            ]);
        }

        return view('dashboard.activities', compact('logs', 'colors', 'actions', 'modules', 'canDelete'));
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

        $exports = ExportLog::with(['user:id,nama', 'jenisInventaris:id,nama'])
            ->whereIn('tipe_export', ['excel', 'pdf'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Determine jenis name: real (DB) or virtual (sistem)
                if ($item->jenis_inventaris_id) {
                    // Real jenis dari database
                    $jenis = $item->jenisInventaris->nama ?? 'Unknown';
                } else {
                    // Virtual jenis - lookup dari filters
                    $filters = is_array($item->filters) ? $item->filters : [];
                    $jenis = $filters['virtual_export_as'] ?? 'Semua Jenis';
                }
                
                return [
                    'user' => $item->user->nama ?? 'Unknown',
                    'jenis' => $jenis,
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

        abort_unless($user && $user->isSuperAdmin(), 403);
    }
}
