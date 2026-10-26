<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBidangRequest;
use App\Http\Requests\UpdateBidangRequest;
use App\Models\Bidang;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Bidang::class);
        // Build query
        $query = Bidang::withCount('users');
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', '%' . $search . '%');
        }
        
        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'with_users') {
                $query->has('users');
            } elseif ($request->status === 'no_users') {
                $query->doesntHave('users');
            }
        }
        
        // Sort order
        $sortBy = $request->get('sort', 'terbaru');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } elseif ($sortBy === 'nama_asc') {
            $query->orderBy('nama', 'asc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        // Paginate items per page
        $bidangsPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $bToday = Bidang::where('created_at', '>=', $todayStart)->count();
        $bYesterday = Bidang::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $bDiff = $bToday - $bYesterday;
        $bPercent = $bYesterday > 0 ? round(($bDiff / $bYesterday) * 100, 1) : ($bToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $calcMonthlyTrend = function($queryCallback) use ($thisMonthStart, $lastMonthStart) {
            $thisMonth = $queryCallback(Bidang::query()->where('created_at', '>=', $thisMonthStart))->count();
            $lastMonth = $queryCallback(Bidang::query()->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart))->count();
            $diff = $thisMonth - $lastMonth;
            $percent = $lastMonth > 0 ? round(($diff / $lastMonth) * 100, 1) : ($thisMonth > 0 ? 100 : 0);
            return ['diff' => $diff, 'percent' => $percent];
        };

        $userBidangThisMonth = \App\Models\User::whereNotNull('bidang_id')->where('created_at', '>=', $thisMonthStart)->count();
        $userBidangLastMonth = \App\Models\User::whereNotNull('bidang_id')->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
        $userBidangDiff = $userBidangThisMonth - $userBidangLastMonth;
        $userBidangPercent = $userBidangLastMonth > 0 ? round(($userBidangDiff / $userBidangLastMonth) * 100, 1) : ($userBidangThisMonth > 0 ? 100 : 0);

        $trends = [
            'totalBidangs'    => $calcMonthlyTrend(fn($q) => $q),
            'totalUsers'      => ['diff' => $userBidangDiff, 'percent' => $userBidangPercent],
            'bidangWithUsers' => $calcMonthlyTrend(fn($q) => $q->has('users')),
        ];

        $topBidang = Bidang::withCount('users')->orderByDesc('users_count')->first();

        // Get total counts
        $stats = [
            'totalBidangs'       => Bidang::count(),
            'totalUsers'         => \App\Models\User::whereNotNull('bidang_id')->count(),
            'bidangWithUsers'    => Bidang::has('users')->count(),
            'bidangWithoutUsers' => Bidang::doesntHave('users')->count(),
            'topBidangNama'      => $topBidang->nama ?? '-',
            'topBidangCount'     => (int) ($topBidang->users_count ?? 0),
            'trends'             => $trends,
        ];
        
        // Icon colors for cycling
        $iconColors = [
            'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
            'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
            'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
            'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)',
            'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
        ];
        
        // Transform paginated bidangs with computed properties
        $bidangsPaginated->through(function($bidang, $index) use ($iconColors) {
            // Generate icon (first letter)
            $bidang->icon_letter = strtoupper(substr($bidang->nama, 0, 1));
            $bidang->icon_color = $iconColors[$index % count($iconColors)];
            
            return $bidang;
        });
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'bidangs' => $bidangsPaginated->items(),
                'stats'   => $stats,
                'pagination' => [
                    'firstItem'   => $bidangsPaginated->firstItem(),
                    'lastItem'    => $bidangsPaginated->lastItem(),
                    'currentPage' => $bidangsPaginated->currentPage(),
                    'lastPage'    => $bidangsPaginated->lastPage(),
                    'perPage'     => $bidangsPaginated->perPage(),
                    'total'       => $bidangsPaginated->total(),
                ],
            ]);
        }
        
        return view('bidangs.index', [
            'bidangs' => $bidangsPaginated,
            'stats' => $stats,
        ]);
    }

    /**
     * Download template resmi CSV (.csv) untuk import master bidang.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Bidang::class);
        $fileName = 'Template_Import_Bidang_' . date('Ymd_His') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BidangTemplateExport(), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Proses import data bidang dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function import(Request $request)
    {
        $this->authorize('create', Bidang::class);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih file CSV (.csv) untuk diimpor.',
            'file_excel.file'     => 'File yang diunggah tidak valid.',
            'file_excel.mimes'    => 'Format file harus berupa CSV (.csv). Format Excel (.xlsx/.xls) tidak didukung.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $importer = new \App\Imports\BidangImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file_excel'));

            // Log aktivitas
            ActivityLogger::log(
                'Bidang',
                'tambah',
                "Import CSV: {$importer->importedCount} bidang berhasil diimpor, {$importer->skippedCount} dilewati (duplikat), {$importer->failedCount} gagal."
            );

            $resultData = [
                'success' => true,
                'message' => "Proses impor selesai. {$importer->importedCount} bidang berhasil ditambahkan ke sistem.",
                'stats' => [
                    'total_rows'     => $importer->totalRows,
                    'imported_count' => $importer->importedCount,
                    'skipped_count'  => $importer->skippedCount,
                    'failed_count'   => $importer->failedCount,
                ],
                'imported_rows' => $importer->importedRows,
                'skipped_rows'  => $importer->skippedRows,
                'failed_rows'   => $importer->failedRows,
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($resultData);
            }

            return redirect()->route('bidangs.index')
                ->with('import_result', $resultData)
                ->with('success', "Proses impor selesai: {$importer->importedCount} bidang berhasil diimpor.");

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Import Bidang error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = $e instanceof \InvalidArgumentException
                ? $e->getMessage()
                : 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    public function create()
    {
        $this->authorize('create', Bidang::class);
        return view('bidangs.create');
    }

    public function store(StoreBidangRequest $request)
    {
        $this->authorize('create', Bidang::class);
        $bidang = Bidang::create($request->validated());

        ActivityLogger::log('Bidang', 'tambah', 'Menambahkan bidang ' . $bidang->nama, $bidang->id, $bidang->nama);

        return redirect()->route('bidangs.index')
            ->with('success', 'Bidang berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $bidang = Bidang::withCount('users')
            ->with(['users' => function($query) {
                $query->with('role')->orderBy('nama', 'asc');
            }])
            ->findOrFail($id);

        $this->authorize('view', $bidang);
        return view('bidangs.show', compact('bidang'));
    }

    public function edit(string $id)
    {
        $bidang = Bidang::withCount('users')->findOrFail($id);
        $this->authorize('update', $bidang);
        return view('bidangs.edit', compact('bidang'));
    }

    public function update(UpdateBidangRequest $request, string $id)
    {
        $bidang = Bidang::findOrFail($id);
        $this->authorize('update', $bidang);
        $bidang->update($request->validated());

        ActivityLogger::log('Bidang', 'ubah', 'Mengubah bidang ' . $bidang->nama, $bidang->id, $bidang->nama);

        return redirect()->route('bidangs.index')
            ->with('success', 'Bidang berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $bidang = Bidang::withCount('users')->findOrFail($id);
        $this->authorize('delete', $bidang);
        if ($bidang->users_count > 0) {
            return redirect()->route('bidangs.index')
                ->with('error', "Tidak dapat menghapus bidang {$bidang->nama}. Masih ada {$bidang->users_count} user yang terkait.");
        }

        $bidangNama = $bidang->nama;
        $bidangId = $bidang->id;
        $bidang->delete();
        ActivityLogger::log('Bidang', 'hapus', 'Menghapus bidang ' . $bidangNama, $bidangId, $bidangNama);
        
        return redirect()->route('bidangs.index')
            ->with('success', 'Bidang berhasil dihapus');
    }
    
    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', Bidang::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }
        $ids = array_slice($ids, 0, 100);
        
        $bidangs = Bidang::withCount('users')->whereIn('id', $ids)->get();
        
        $deleted = 0;
        $skipped = 0;
        $skippedNames = [];
        
        foreach ($bidangs as $bidang) {
            if ($bidang->users_count > 0) {
                $skipped++;
                $skippedNames[] = $bidang->nama;
            } else {
                $bidangNama = $bidang->nama;
                $bidangId = $bidang->id;
                $bidang->delete();
                ActivityLogger::log('Bidang', 'hapus', 'Menghapus bidang ' . $bidangNama, $bidangId, $bidangNama);
                $deleted++;
            }
        }
        
        $message = '';
        if ($deleted > 0) {
            $message .= "{$deleted} bidang berhasil dihapus. ";
        }
        if ($skipped > 0) {
            $message .= "{$skipped} bidang tidak dapat dihapus karena masih memiliki user terkait (" . implode(', ', $skippedNames) . ").";
        }
        
        $type = $deleted > 0 ? 'success' : 'warning';
        
        return redirect()->route('bidangs.index')
            ->with($type, $message);
    }

    /**
     * Ekspor rekapitulasi data master bidang ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Bidang::class);
        $format = strtolower($request->query('format', 'excel'));

        $query = Bidang::withCount('users');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $bidangs = $query->orderBy('id', 'asc')->get();
        $dateStr = date('Ymd_His');

        ActivityLogger::log('Bidang', 'export', 'Mengekspor rekapitulasi bidang (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $headers = [
                ['text' => 'NO', 'width' => '6%', 'align' => 'center'],
                ['text' => 'NAMA BIDANG', 'width' => '38%', 'align' => 'left'],
                ['text' => 'DESKRIPSI', 'width' => '34%', 'align' => 'left'],
                ['text' => 'JML PENGGUNA / STAF', 'width' => '22%', 'align' => 'center'],
            ];

            $rows = [];
            foreach ($bidangs as $i => $item) {
                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $item->nama ?? '-'],
                    ['text' => $item->deskripsi ?: '-'],
                    ['text' => $item->users_count ?? 0, 'class' => 'text-center'],
                ];
            }

            $summary = [
                'Total Bidang' => count($bidangs) . ' Bidang',
            ];

            return \App\Services\FeatureExportService::downloadPdf(
                'Laporan Rekapitulasi Bidang Pemerintahan Desa',
                'Master Data Bidang Tugas dan Tanggung Jawab Pengelolaan Aset',
                $headers,
                $rows,
                "Rekap_Bidang_{$dateStr}.pdf",
                'portrait',
                $summary
            );
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BidangExport($bidangs),
            "Rekap_Bidang_{$dateStr}.xlsx"
        );
    }
}

