<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\JenisInventaris;
use App\Models\Ruangan;
use App\Services\ActivityLogger;
use App\Exports\RuanganExport;
use App\Services\FeatureExportService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ruangan::class);

        $query = Ruangan::withCount('inventaris')->with('penanggungJawab');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'terisi') {
                $query->has('inventaris');
            } elseif ($request->status === 'kosong') {
                $query->doesntHave('inventaris');
            }
        }

        $sortBy = $request->get('sort', 'terbaru');
        match ($sortBy) {
            'nama_desc' => $query->orderBy('nama', 'desc'),
            'nama_asc'  => $query->orderBy('nama', 'asc'),
            'terlama'   => $query->orderBy('created_at', 'asc'),
            default     => $query->orderBy('created_at', 'desc'),
        };

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $ruangans = $query->paginate($perPage)->withQueryString();

        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $ruanganToday = Ruangan::where('created_at', '>=', $todayStart)->count();
        $ruanganYesterday = Ruangan::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $ruanganDiff = $ruanganToday - $ruanganYesterday;
        $ruanganPercent = $ruanganYesterday > 0 ? round(($ruanganDiff / $ruanganYesterday) * 100, 1) : ($ruanganToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $calcMonthlyTrend = function($queryCallback) use ($thisMonthStart, $lastMonthStart) {
            $thisMonth = $queryCallback(Ruangan::query()->where('created_at', '>=', $thisMonthStart))->count();
            $lastMonth = $queryCallback(Ruangan::query()->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart))->count();
            $diff = $thisMonth - $lastMonth;
            $percent = $lastMonth > 0 ? round(($diff / $lastMonth) * 100, 1) : ($thisMonth > 0 ? 100 : 0);
            return ['diff' => $diff, 'percent' => $percent];
        };

        // Monthly trend for aset di ruangan
        $asetRuanganThisMonth = \App\Models\Inventaris::whereNotNull('ruangan_id')->where('created_at', '>=', $thisMonthStart)->count();
        $asetRuanganLastMonth = \App\Models\Inventaris::whereNotNull('ruangan_id')->where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
        $asetRuanganDiff = $asetRuanganThisMonth - $asetRuanganLastMonth;
        $asetRuanganPercent = $asetRuanganLastMonth > 0 ? round(($asetRuanganDiff / $asetRuanganLastMonth) * 100, 1) : ($asetRuanganThisMonth > 0 ? 100 : 0);

        $trends = [
            'total'         => $calcMonthlyTrend(fn($q) => $q),
            'totalAset'     => ['diff' => $asetRuanganDiff, 'percent' => $asetRuanganPercent],
            'ruanganTerisi' => $calcMonthlyTrend(fn($q) => $q->has('inventaris')),
            'ruanganKosong' => $calcMonthlyTrend(fn($q) => $q->doesntHave('inventaris')),
        ];

        $stats = [
            'totalRuangan'    => Ruangan::count(),
            'totalAset'       => \App\Models\Inventaris::whereNotNull('ruangan_id')->count(),
            'ruanganTerisi'   => Ruangan::has('inventaris')->count(),
            'ruanganKosong'   => Ruangan::doesntHave('inventaris')->count(),
            'trends'          => $trends,
        ];

        if ($request->ajax()) {
            return response()->json([
                'ruangans'   => $ruangans->map(function($r) {
                    return [
                        'id'              => $r->id,
                        'nama'            => $r->nama,
                        'kode'            => $r->kode,
                        'deskripsi'       => $r->deskripsi,
                        'inventaris_count'=> $r->inventaris_count,
                        'initials'        => $r->initials,
                        'penanggung_jawab'=> $r->penanggungJawab?->nama ?? null,
                    ];
                })->values(),
                'stats'      => $stats,
                'pagination' => [
                    'firstItem'   => $ruangans->firstItem(),
                    'lastItem'    => $ruangans->lastItem(),
                    'currentPage' => $ruangans->currentPage(),
                    'lastPage'    => $ruangans->lastPage(),
                    'perPage'     => $ruangans->perPage(),
                    'total'       => $ruangans->total(),
                ],
            ]);
        }

        return view('ruangan.index', compact('ruangans', 'stats'));
    }

    /**
     * Download template resmi CSV (.csv) untuk import master ruangan.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Ruangan::class);
        $fileName = 'Template_Import_Ruangan_' . date('Ymd_His') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RuanganTemplateExport(), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Proses import data ruangan dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function import(Request $request)
    {
        $this->authorize('create', Ruangan::class);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih file CSV (.csv) untuk diimpor.',
            'file_excel.file'     => 'File yang diunggah tidak valid.',
            'file_excel.mimes'    => 'Format file harus berupa CSV (.csv). Format Excel (.xlsx/.xls) tidak didukung.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $importer = new \App\Imports\RuanganImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file_excel'));

            // Log aktivitas
            ActivityLogger::log(
                'Ruangan',
                'tambah',
                "Import CSV: {$importer->importedCount} data berhasil diimpor, {$importer->skippedCount} dilewati (duplikat), {$importer->failedCount} gagal."
            );

            $resultData = [
                'success' => true,
                'message' => "Proses impor selesai. {$importer->importedCount} ruangan berhasil ditambahkan ke sistem.",
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

            return redirect()->route('ruangan.index')
                ->with('import_result', $resultData)
                ->with('success', "Proses impor selesai: {$importer->importedCount} ruangan berhasil diimpor.");

        } catch (\Throwable $e) {
            Log::error('Import Ruangan error: ' . $e->getMessage(), [
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
        $this->authorize('create', Ruangan::class);
        $userList = \App\Models\User::excludeSuperDevelopers()->where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);
        
        $lastKode = Ruangan::whereNotNull('kode')
            ->pluck('kode')
            ->filter(function($k) {
                return preg_match('/\d+/', (string)$k);
            })
            ->map(function($k) {
                if (preg_match('/^(.*?)(\d+)$/', trim((string)$k), $matches)) {
                    return [
                        'original' => $k,
                        'prefix'   => $matches[1],
                        'digitStr' => $matches[2],
                        'num'      => (int) $matches[2],
                    ];
                }
                preg_match('/(\d+)/', (string)$k, $matches);
                return [
                    'original' => $k,
                    'prefix'   => '',
                    'digitStr' => $matches[1],
                    'num'      => (int) $matches[1],
                ];
            })
            ->sortByDesc('num')
            ->first();

        $lastKodeRuangan = $lastKode ? $lastKode['original'] : null;
        if ($lastKode) {
            $prefix   = $lastKode['prefix'];
            $digitLen = strlen($lastKode['digitStr']);
            $nextNum  = $lastKode['num'] + 1;
            $nextSuggestedKode = $prefix . str_pad((string)$nextNum, $digitLen, '0', STR_PAD_LEFT);
        } else {
            $nextSuggestedKode = 'R-001';
        }

        return view('ruangan.create', compact('userList', 'lastKodeRuangan', 'nextSuggestedKode'));
    }

    public function store(StoreRuanganRequest $request)
    {
        $this->authorize('create', Ruangan::class);
        try {
            $validated = $request->validated();
            $ruangan = Ruangan::create($validated);
            ActivityLogger::log('Ruangan', 'tambah', 'Menambahkan ruangan ' . $ruangan->nama, $ruangan->id, $ruangan->nama);
            return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil ditambahkan!');
        } catch (\Throwable $e) {
            Log::error('RuanganController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan ruangan. Silakan coba lagi.');
        }
    }

    public function show(Ruangan $ruangan, \Illuminate\Http\Request $request)
    {
        $this->authorize('view', $ruangan);
        $ruangan->load('penanggungJawab');

        $perPage = in_array((int)$request->input('per_page'), [10, 20, 50, 100]) ? (int)$request->input('per_page') : 10;

        // Paginate aset di ruangan ini, sorted by nama jenis lalu kode barang
        $inventaris = $ruangan->inventaris()
            ->with('jenis')
            ->leftJoin('jenis_inventaris', 'inventaris.jenis_inventaris_id', '=', 'jenis_inventaris.id')
            ->orderBy('jenis_inventaris.nama', 'asc')
            ->orderBy('inventaris.kode_barang', 'asc')
            ->select('inventaris.*')
            ->paginate($perPage)
            ->withQueryString();

        // AJAX request → return JSON untuk partial update tabel
        if ($request->ajax() || $request->wantsJson()) {
            $items = $inventaris->map(function ($aset) {
                return [
                    'id'           => $aset->id,
                    'display_name' => $aset->display_name ?? '-',
                    'jenis_nama'   => $aset->jenis?->nama ?? '-',
                    'kode_barang'  => $aset->kode_barang ?? '-',
                    'show_url'     => route('manajemen-aset.show', $aset->id),
                ];
            });
            return response()->json([
                'items'        => $items,
                'firstItem'    => $inventaris->firstItem(),
                'lastItem'     => $inventaris->lastItem(),
                'total'        => $inventaris->total(),
                'currentPage'  => $inventaris->currentPage(),
                'lastPage'     => $inventaris->lastPage(),
                'perPage'      => $inventaris->perPage(),
                'prevPageUrl'  => $inventaris->previousPageUrl(),
                'nextPageUrl'  => $inventaris->nextPageUrl(),
                'pageUrls'     => collect($inventaris->getUrlRange(
                    max(1, $inventaris->currentPage() - 2),
                    min($inventaris->lastPage(), $inventaris->currentPage() + 2)
                ))->map(fn($url, $page) => ['page' => $page, 'url' => $url])->values(),
            ]);
        }

        return view('ruangan.show', compact('ruangan', 'inventaris'));
    }

    public function edit(Ruangan $ruangan)
    {
        $this->authorize('update', $ruangan);
        $userList = \App\Models\User::excludeSuperDevelopers()->where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);
        return view('ruangan.edit', compact('ruangan', 'userList'));
    }

    public function update(UpdateRuanganRequest $request, Ruangan $ruangan)
    {
        $this->authorize('update', $ruangan);
        try {
            $ruangan->update($request->validated());
            ActivityLogger::log('Ruangan', 'ubah', 'Mengubah ruangan ' . $ruangan->nama, $ruangan->id, $ruangan->nama);
            return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
        } catch (\Throwable $e) {
            Log::error('RuanganController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui ruangan. Silakan coba lagi.');
        }
    }

    public function destroy(Ruangan $ruangan)
    {
        $this->authorize('delete', $ruangan);

        $jumlahAset = $ruangan->inventaris()->count();
        if ($jumlahAset > 0) {
            return redirect()->back()->with('error',
                "Tidak dapat menghapus ruangan '{$ruangan->nama}' karena masih memiliki {$jumlahAset} aset terdaftar.");
        }

        try {
            $namaRuangan = $ruangan->nama;
            $idRuangan = $ruangan->id;
            $ruangan->delete();
            ActivityLogger::log('Ruangan', 'hapus', 'Menghapus ruangan ' . $namaRuangan, $idRuangan, $namaRuangan);
            return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil dihapus!');
        } catch (\Throwable $e) {
            Log::error('RuanganController::destroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus ruangan. Silakan coba lagi.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', Ruangan::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids     = array_slice((array) $request->input('ids', []), 0, 100);
        $deleted = 0;
        $skipped = [];

        foreach ($ids as $id) {
            $ruangan = Ruangan::find($id);
            if (!$ruangan) continue;
            if ($ruangan->inventaris()->count() > 0) {
                $skipped[] = $ruangan->nama . ' (masih memiliki aset)';
                continue;
            }
            $namaRuangan = $ruangan->nama;
            $idRuangan = $ruangan->id;
            $ruangan->delete();
            ActivityLogger::log('Ruangan', 'hapus', 'Menghapus ruangan ' . $namaRuangan, $idRuangan, $namaRuangan);
            $deleted++;
        }

        $msg = $deleted . ' ruangan berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Tidak dapat dihapus: ' . implode(', ', $skipped) . '.';
        }

        return redirect()->route('ruangan.index')->with($deleted > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Ekspor rekapitulasi data master ruangan ke format PDF atau Excel.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Ruangan::class);
        $format = strtolower($request->query('format', 'excel'));

        $inventarisFilter = function ($q) {
            $q->whereHas('jenis', fn($j) => $j->where('nama', '!=', JenisInventaris::NAMA_TIDAK_DITEMUKAN));
        };

        $query = Ruangan::withCount(['inventaris' => $inventarisFilter])
            ->with('penanggungJawab')
            ->withSum(['inventaris' => $inventarisFilter], 'nilai_perolehan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'terisi') {
                $query->whereHas('inventaris', $inventarisFilter);
            } elseif ($request->status === 'kosong') {
                $query->whereDoesntHave('inventaris', $inventarisFilter);
            }
        }

        $ruangans = $query->orderBy('id', 'asc')->get();
        $dateStr = date('Ymd_His');

        ActivityLogger::log('Ruangan', 'export', 'Mengekspor rekapitulasi ruangan (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $headers = [
                ['text' => 'NO', 'width' => '4%', 'align' => 'center'],
                ['text' => 'KODE', 'width' => '10%', 'align' => 'center'],
                ['text' => 'NAMA RUANGAN', 'width' => '22%', 'align' => 'left'],
                ['text' => 'PENANGGUNG JAWAB', 'width' => '20%', 'align' => 'left'],
                ['text' => 'JML ASET', 'width' => '10%', 'align' => 'center'],
                ['text' => 'TOTAL NILAI ASET (RP)', 'width' => '18%', 'align' => 'right'],
                ['text' => 'KETERANGAN', 'width' => '16%', 'align' => 'left'],
            ];

            $rows = [];
            $totalAset = 0;
            $totalNilai = 0;

            foreach ($ruangans as $i => $item) {
                $count = (int) ($item->inventaris_count ?? 0);
                $nilai = (float) ($item->inventaris_sum_nilai_perolehan ?? 0);
                $totalAset += $count;
                $totalNilai += $nilai;

                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $item->kode ?: '-', 'class' => 'text-center'],
                    ['text' => $item->nama ?? '-'],
                    ['text' => $item->penanggungJawab?->nama ?? '-'],
                    ['text' => number_format($count, 0, ',', '.'), 'class' => 'text-center'],
                    ['text' => $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '-', 'class' => 'text-right'],
                    ['text' => $item->deskripsi ?: '-'],
                ];
            }

            // Baris Total Keseluruhan
            $rows[] = [
                ['text' => 'TOTAL KESELURUHAN', 'class' => 'text-center font-bold', 'colspan' => 4],
                ['text' => number_format($totalAset, 0, ',', '.'), 'class' => 'text-center font-bold'],
                ['text' => number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'), 'class' => 'text-right font-bold'],
                ['text' => '-', 'class' => 'text-center font-bold'],
            ];

            $summary = [
                'Total Ruangan' => count($ruangans) . ' Ruangan',
                'Total Aset di Ruangan' => number_format($totalAset, 0, ',', '.') . ' Unit',
                'Akumulasi Nilai Aset' => 'Rp ' . number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'),
            ];

            return FeatureExportService::downloadPdf(
                'Laporan Rekapitulasi Master Ruangan & Penempatan Aset Desa',
                'Master Data Ruangan dan Inventarisasi Penempatan Fisik Barang',
                $headers,
                $rows,
                'Laporan_Rekapitulasi_Ruangan_' . $dateStr . '.pdf',
                'portrait',
                $summary
            );
        }

        return Excel::download(new RuanganExport($ruangans), 'Rekapitulasi_Ruangan_' . $dateStr . '.xlsx');
    }
}

