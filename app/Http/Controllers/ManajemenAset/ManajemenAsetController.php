<?php

namespace App\Http\Controllers\ManajemenAset;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventarisRequest;
use App\Http\Requests\UpdateInventarisRequest;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\InventarisValue;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JenisInventarisPejabat;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\InventarisFoto;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use App\Exports\InventarisTemplateExport;
use App\Imports\InventarisImport;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenAsetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Inventaris::class);

        // Handle flash message dari operasi AJAX (salin data)
        if ($request->filled('_flash_success')) {
            session()->flash('success', $request->input('_flash_success'));
            return redirect()->route('manajemen-aset.index');
        }
        
        // Build query with filters
        $query = Inventaris::with(['jenis', 'ruangan', 'values.field']);
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%')
                  ->orWhere('nup', 'like', '%' . $search . '%')
                  ->orWhereHas('jenis', function($jq) use ($search) {
                      $jq->where('nama', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('ruangan', function($rq) use ($search) {
                      $rq->where('nama', 'like', '%' . $search . '%')
                         ->orWhere('kode', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Jenis filter
        if ($request->has('jenis') && is_array($request->jenis) && count($request->jenis) > 0) {
            $query->whereIn('jenis_inventaris_id', $request->jenis);
        }

        // Tahun perolehan filter
        if ($request->filled('tahun') && $request->tahun !== 'all') {
            $query->where('tahun_perolehan', $request->tahun);
        }

        // Ruangan filter
        if ($request->filled('ruangan') && $request->ruangan !== 'all') {
            if ($request->ruangan === 'none') {
                $query->whereNull('ruangan_id');
            } else {
                $query->where('ruangan_id', $request->ruangan);
            }
        }

        // Kondisi filter (supports array for checklist e.g. kondisi[]=baik&kondisi[]=rusak_ringan)
        if ($request->has('kondisi')) {
            $kondisi = (array) $request->kondisi;
            $validKondisi = array_values(array_filter($kondisi, fn($k) => in_array(strtolower($k), ['baik', 'rusak_ringan', 'rusak_berat'])));
            if (!empty($validKondisi)) {
                $query->where(function($q) use ($validKondisi) {
                    foreach ($validKondisi as $k) {
                        $clean = strtolower($k);
                        $title = ucwords(str_replace('_', ' ', $clean));
                        $q->orWhere('kondisi', $clean)
                          ->orWhere('kondisi', $title)
                          ->orWhere('kondisi', ucfirst($clean));
                    }
                });
            }
        }

        // Kelengkapan filter ('lengkap' | 'tidak_lengkap')
        if ($request->filled('kelengkapan') && in_array($request->kelengkapan, ['lengkap', 'tidak_lengkap'])) {
            $query->kelengkapan($request->kelengkapan);
        }
        
        // Sort order
        $sortBy = $request->get('sort', 'terbaru');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama_barang', 'desc');
        } elseif ($sortBy === 'nama_asc') {
            $query->orderBy('nama_barang', 'asc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        // Paginate with items per page
        $inventarisPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();

        // Total Inventaris Daily Change
        $totalInv = Inventaris::count();
        $invToday = Inventaris::where('created_at', '>=', $todayStart)->count();
        $invYesterday = Inventaris::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $invDiff = $invToday - $invYesterday;
        $invPercent = $invYesterday > 0 ? round(($invDiff / $invYesterday) * 100, 1) : ($invToday > 0 ? 100 : 0);

        // Kondisi Baik Daily Change
        $baikTotal = Inventaris::where('kondisi', 'baik')->count();
        $baikToday = Inventaris::where('kondisi', 'baik')->where('created_at', '>=', $todayStart)->count();
        $baikYesterday = Inventaris::where('kondisi', 'baik')->whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $baikDiff = $baikToday - $baikYesterday;
        $baikPercent = $baikYesterday > 0 ? round(($baikDiff / $baikYesterday) * 100, 1) : ($baikToday > 0 ? 100 : 0);

        // Kondisi Rusak Ringan Daily Change
        $rrTotal = Inventaris::where('kondisi', 'rusak_ringan')->count();
        $rrToday = Inventaris::where('kondisi', 'rusak_ringan')->where('created_at', '>=', $todayStart)->count();
        $rrYesterday = Inventaris::where('kondisi', 'rusak_ringan')->whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $rrDiff = $rrToday - $rrYesterday;
        $rrPercent = $rrYesterday > 0 ? round(($rrDiff / $rrYesterday) * 100, 1) : ($rrToday > 0 ? 100 : 0);

        // Kondisi Rusak Berat Daily Change
        $rbTotal = Inventaris::where('kondisi', 'rusak_berat')->count();
        $rbToday = Inventaris::where('kondisi', 'rusak_berat')->where('created_at', '>=', $todayStart)->count();
        $rbYesterday = Inventaris::where('kondisi', 'rusak_berat')->whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $rbDiff = $rbToday - $rbYesterday;
        $rbPercent = $rbYesterday > 0 ? round(($rbDiff / $rbYesterday) * 100, 1) : ($rbToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu) - Dioptimalkan dalam 1 query agregasi cepat
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $statsRaw = DB::table('inventaris')
            ->selectRaw("
                COUNT(*) as total_inv,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as inv_this_month,
                SUM(CASE WHEN created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as inv_last_month,
                SUM(CASE WHEN LOWER(kondisi) = 'baik' THEN 1 ELSE 0 END) as baik_total,
                SUM(CASE WHEN LOWER(kondisi) = 'baik' AND created_at >= ? THEN 1 ELSE 0 END) as baik_this_month,
                SUM(CASE WHEN LOWER(kondisi) = 'baik' AND created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as baik_last_month,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_ringan' THEN 1 ELSE 0 END) as rr_total,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_ringan' AND created_at >= ? THEN 1 ELSE 0 END) as rr_this_month,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_ringan' AND created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as rr_last_month,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_berat' THEN 1 ELSE 0 END) as rb_total,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_berat' AND created_at >= ? THEN 1 ELSE 0 END) as rb_this_month,
                SUM(CASE WHEN LOWER(kondisi) = 'rusak_berat' AND created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as rb_last_month
            ", [
                $thisMonthStart,
                $lastMonthStart, $thisMonthStart,
                $thisMonthStart,
                $lastMonthStart, $thisMonthStart,
                $thisMonthStart,
                $lastMonthStart, $thisMonthStart,
                $thisMonthStart,
                $lastMonthStart, $thisMonthStart,
            ])
            ->first();

        $totalInv = (int) ($statsRaw->total_inv ?? 0);
        $invThisMonth = (int) ($statsRaw->inv_this_month ?? 0);
        $invLastMonth = (int) ($statsRaw->inv_last_month ?? 0);
        $invDiff = $invThisMonth - $invLastMonth;
        $invPercent = $invLastMonth > 0 ? round(($invDiff / $invLastMonth) * 100, 1) : ($invThisMonth > 0 ? 100 : 0);

        $baikTotal = (int) ($statsRaw->baik_total ?? 0);
        $baikThisMonth = (int) ($statsRaw->baik_this_month ?? 0);
        $baikLastMonth = (int) ($statsRaw->baik_last_month ?? 0);
        $baikDiff = $baikThisMonth - $baikLastMonth;
        $baikPercent = $baikLastMonth > 0 ? round(($baikDiff / $baikLastMonth) * 100, 1) : ($baikThisMonth > 0 ? 100 : 0);

        $rrTotal = (int) ($statsRaw->rr_total ?? 0);
        $rrThisMonth = (int) ($statsRaw->rr_this_month ?? 0);
        $rrLastMonth = (int) ($statsRaw->rr_last_month ?? 0);
        $rrDiff = $rrThisMonth - $rrLastMonth;
        $rrPercent = $rrLastMonth > 0 ? round(($rrDiff / $rrLastMonth) * 100, 1) : ($rrThisMonth > 0 ? 100 : 0);

        $rbTotal = (int) ($statsRaw->rb_total ?? 0);
        $rbThisMonth = (int) ($statsRaw->rb_this_month ?? 0);
        $rbLastMonth = (int) ($statsRaw->rb_last_month ?? 0);
        $rbDiff = $rbThisMonth - $rbLastMonth;
        $rbPercent = $rbLastMonth > 0 ? round(($rbDiff / $rbLastMonth) * 100, 1) : ($rbThisMonth > 0 ? 100 : 0);

        // Get total counts with status breakdown & trends
        $stats = [
            'totalInventaris'    => $totalInv,
            'kondisiBaik'        => $baikTotal,
            'kondisiRusakRingan' => $rrTotal,
            'kondisiRusakBerat'  => $rbTotal,
            'totalLengkap'       => Inventaris::kelengkapan('lengkap')->count(),
            'totalTidakLengkap'  => Inventaris::kelengkapan('tidak_lengkap')->count(),
            'trends' => [
                'total' => ['diff' => $invDiff, 'percent' => $invPercent],
                'baik'  => ['diff' => $baikDiff, 'percent' => $baikPercent],
                'rr'    => ['diff' => $rrDiff, 'percent' => $rrPercent],
                'rb'    => ['diff' => $rbDiff, 'percent' => $rbPercent],
            ],
        ];
        
        // Get jenis list for filter (exclude virtual jenis)
        $jenisList = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                                     ->orderBy('nama')
                                     ->get();

        // Get tahun list and ruangan list for filter
        $tahunList = Inventaris::whereNotNull('tahun_perolehan')
                               ->select('tahun_perolehan')
                               ->distinct()
                               ->orderBy('tahun_perolehan', 'desc')
                               ->pluck('tahun_perolehan');
        $ruanganList = \App\Models\Ruangan::orderBy('nama')->get(['id', 'nama', 'kode']);
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            $items = collect($inventarisPaginated->items())->map(function($item) {
                $jenis = $item->jenis;
                $colors = $jenis ? ($jenis->color_gradient ?? ['#6b7280','#4b5563']) : ['#6b7280','#4b5563'];
                $isLengkap = $item->isDataLengkap();
                return [
                    'id'              => $item->id,
                    'display_name'    => $item->display_name ?? ($item->nama_barang ?? '-'),
                    'nama_barang'     => $item->nama_barang ?? ($item->display_name ?? '-'),
                    'nup'             => $item->nup ?? '-',
                    'kode_barang'     => $item->kode_barang ?? '-',
                    'tahun_perolehan' => $item->tahun_perolehan ? (string)$item->tahun_perolehan : '-',
                    'nilai_perolehan' => (float)($item->nilai_perolehan ?? 0),
                    'kondisi'         => $item->kondisi ?? 'Baik',
                    'is_lengkap'      => $isLengkap,
                    'missing_fields'  => $isLengkap ? [] : $item->getMissingRequiredFields(),
                    'ruangan_id'      => $item->ruangan_id,
                    'ruangan_nama'    => $item->ruangan ? $item->ruangan->nama_with_kode : '-',
                    'jenis_nama'      => $jenis->nama ?? 'Unknown',
                    'jenis_icon'      => $jenis ? ($jenis->icon ?? 'box') : 'box',
                    'jenis_color0'    => $colors[0],
                    'jenis_color1'    => $colors[1],
                ];
            });
            return response()->json([
                'inventaris'   => $items,
                'stats'        => $stats,
                'total'        => $inventarisPaginated->total(),
                'from'         => $inventarisPaginated->firstItem(),
                'to'           => $inventarisPaginated->lastItem(),
                'pagination'   => [
                    'firstItem'   => $inventarisPaginated->firstItem(),
                    'lastItem'    => $inventarisPaginated->lastItem(),
                    'currentPage' => $inventarisPaginated->currentPage(),
                    'lastPage'    => $inventarisPaginated->lastPage(),
                    'perPage'     => $inventarisPaginated->perPage(),
                    'total'       => $inventarisPaginated->total(),
                ],
            ]);
        }
        
        return view('manajemen-aset.index', [
            'inventaris'  => $inventarisPaginated,
            'stats'       => $stats,
            'jenisList'   => $jenisList,
            'tahunList'   => $tahunList,
            'ruanganList' => $ruanganList,
        ]);
    }

    /**
     * Download template resmi CSV (.csv) untuk import inventaris aset.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Inventaris::class);
        $fileName = 'Template_Import_Inventaris_Desa_' . date('Ymd_His') . '.csv';
        return Excel::download(new InventarisTemplateExport(), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Proses import data inventaris dari file Excel (.xlsx) atau CSV (.csv).
     */
    public function import(Request $request)
    {
        $this->authorize('create', Inventaris::class);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih file CSV (.csv) untuk diimpor.',
            'file_excel.file'     => 'File yang diunggah tidak valid.',
            'file_excel.mimes'    => 'Format file harus berupa CSV (.csv). Format Excel (.xlsx/.xls) tidak didukung.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $importer = new InventarisImport();
            Excel::import($importer, $request->file('file_excel'));

            // Clear dashboard cache agar angka statistik langsung terupdate
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');

            // Log aktivitas
            ActivityLogger::log(
                'Inventaris',
                'tambah',
                "Import CSV: {$importer->importedCount} data berhasil diimpor, {$importer->skippedCount} dilewati (duplikat), {$importer->failedCount} gagal."
            );

            $resultData = [
                'success' => true,
                'message' => "Proses impor selesai. {$importer->importedCount} data berhasil ditambahkan ke sistem.",
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

            return redirect()->route('manajemen-aset.index')
                ->with('import_result', $resultData)
                ->with('success', "Proses impor selesai: {$importer->importedCount} aset berhasil diimpor.");

        } catch (\Throwable $e) {
            Log::error('Import Inventaris error: ' . $e->getMessage(), [
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
        $this->authorize('create', Inventaris::class);
        // Exclude Buku Inventaris Aset Desa (virtual jenis tidak untuk input data individual)
        $jenisList = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                                     ->with('fields')
                                     ->orderBy('nama')
                                     ->get();
        
        // Build pejabatByJenis mapping (pejabat defaults per jenis)
        try {
            $pejabatByJenis = JenisInventarisPejabat::with('pejabat')
                ->get()
                ->groupBy('jenis_inventaris_id')
                ->map(function($items) {
                    return $items->sortBy('order')->map(function($it) {
                        return [
                            'nama' => optional($it->pejabat)->nama ?? '',
                            'jabatan' => optional($it->pejabat)->jabatan ?? '',
                            'order' => $it->order ?? 0,
                        ];
                    })->values()->all();
                })->toArray();
        } catch (\Throwable $e) {
            Log::warning('Failed to build pejabatByJenis: ' . $e->getMessage());
            $pejabatByJenis = [];
        }

        // Daftar user untuk dropdown Pengguna
        $userList = \App\Models\User::excludeSuperDevelopers()->where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);

        // Daftar ruangan untuk dropdown Ruangan
        $ruanganList = \App\Models\Ruangan::orderBy('nama')->get(['id', 'nama', 'kode' /*, 'lokasi'*/]);

        // Build kibConfigByJenis untuk JS — field KIB statis per jenis
        $kibConfigByJenis = [];
        foreach ($jenisList as $jenis) {
            if ($jenis->kib_type) {
                $kibConfigByJenis[$jenis->id] = config('kib.' . $jenis->kib_type, []);
            }
        }

        return view('manajemen-aset.create', compact('jenisList', 'pejabatByJenis', 'userList', 'ruanganList', 'kibConfigByJenis'));
    }

    public function store(StoreInventarisRequest $request)
    {
        $this->authorize('create', Inventaris::class);
        $data = $request->validated();
        $dynamic = $request->input('dynamic', []);

        // Generate a unique barcode string (persistent)
        $barcode = $this->generateUniqueBarcode();

        // debug log - store attempt
        /* Log::debug('Inventaris.store', [
            'user_id' => auth()->id(),
            'jenis_inventaris_id' => $data['jenis_inventaris_id'] ?? null,
            'kode_barang' => $data['kode_barang'] ?? null,
            'has_fotos' => $request->hasFile('fotos'),
            'fotos_count' => is_array($request->file('fotos')) ? count($request->file('fotos')) : ($request->file('fotos') ? 1 : 0),
            'all_files_keys' => array_keys($request->allFiles()),
        ]); */

        DB::beginTransaction();
        try {
            $inventarisData = [
                'jenis_inventaris_id' => $data['jenis_inventaris_id'],
                'jenis_barang' => $data['jenis_barang'] ?? null,
                'nama_barang' => $data['nama_barang'] ?? null,
                'satuan' => $data['satuan'] ?? null,
                'kode_barang' => $data['kode_barang'],
                'nup' => $data['nup'] ?? null,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'asal_usul' => $data['asal_usul'] ?? null,
                'bukti_kepemilikan' => $data['bukti_kepemilikan'] ?? null,
                'pengguna' => $data['pengguna'] ?? null,
                'ruangan_id' => $data['ruangan_id'] ?? null,
                'kondisi' => $data['kondisi'] ?? null,
                // created_by di-set otomatis oleh Inventaris::booted()
            ];
            
            // Add barcode only if column exists
            if (Schema::hasColumn('inventaris', 'barcode')) {
                $inventarisData['barcode'] = $barcode;
            }
            
            $inventaris = Inventaris::create($inventarisData);
            // Set status secara eksplisit (tidak lewat mass assignment)
            // DISABLED: approval system disabled - always set to draft
            $inventaris->status = 'draft';
            $inventaris->save();

            if (!empty($dynamic) && is_array($dynamic)) {
                $fieldNames = array_keys($dynamic);
                $fields = JenisInventarisData::where('jenis_inventaris_id', $inventaris->jenis_inventaris_id)
                    ->whereIn('nama_field', $fieldNames)
                    ->get()
                    ->keyBy('nama_field');

                foreach ($dynamic as $name => $value) {
                    if (!isset($fields[$name])) continue;
                    InventarisValue::updateOrCreate(
                        [
                            'inventaris_id' => $inventaris->id,
                            'jenis_inventaris_data_id' => $fields[$name]->id,
                        ],
                        ['value' => $value]
                    );
                }
            }

            // Simpan KIB values (field statis Buku Inventaris Aset Desa)
            $kibData = $request->input('kib', []);
            if (!empty($kibData) && is_array($kibData)) {
                foreach ($kibData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKibValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kib_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Simpan KIR values
            $kirData = $request->input('kir', []);
            if (!empty($kirData) && is_array($kirData)) {
                foreach ($kirData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKirValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kir_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Simpan Laporan Aset values (bulan perolehan)
            $bulanPerolehan = $request->input('bulan_perolehan');
            if ($bulanPerolehan !== null && $bulanPerolehan !== '') {
                \App\Models\InventarisLaporanAsetValue::updateOrCreate(
                    ['inventaris_id' => $inventaris->id],
                    ['bulan_perolehan' => (int)$bulanPerolehan]
                );
            }

            // Upload foto di dalam transaksi agar bisa di-rollback jika gagal
            // Track file yang sudah terupload agar bisa dihapus jika DB gagal
            $uploadedPaths = [];
            if ($request->hasFile('fotos')) {
                $fotoFiles = array_slice((array) $request->file('fotos'), 0, 5);
                /* Log::debug('Inventaris.store.foto_upload_start', [
                    'inventaris_id' => $inventaris->id,
                    'incoming_count' => count($fotoFiles),
                ]); */

                foreach ($fotoFiles as $i => $foto) {
                    $path = $foto->store('inventaris-foto/' . $inventaris->id, 'public');
                    $uploadedPaths[] = $path;
                    InventarisFoto::create([
                        'inventaris_id' => $inventaris->id,
                        'path'          => $path,
                        'urutan'        => $i,
                    ]);
                }

                /* Log::debug('Inventaris.store.foto_upload_done', [
                    'inventaris_id' => $inventaris->id,
                    'saved_count' => count($uploadedPaths),
                ]); */
            }

            DB::commit();

            Log::info('Inventaris stored', [
                'inventaris_id' => $inventaris->id,
                'created_by' => $inventaris->created_by,
                'dynamic_fields' => array_keys($dynamic),
                'barcode' => $barcode,
            ]);

            // Generate QR code PNG (best-effort: tidak rollback jika gagal)
            try {
                $this->generateQrPng($inventaris->id, $barcode);
            } catch (\Throwable $e) {
                Log::warning('Failed to generate QR PNG for inventaris', [
                    'inventaris_id' => $inventaris->id,
                    'error' => $e->getMessage(),
                ]);
            }

        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file foto yang sudah terlanjur terupload ke disk
            foreach ($uploadedPaths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Inventaris store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => array_merge($data, ['dynamic' => array_keys($dynamic)]),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Gagal menyimpan inventaris. Silakan coba lagi.')->withInput();
        }

        ActivityLogger::log('Inventaris', 'tambah',
            'Menambahkan inventaris ' . ($inventaris->kode_barang ?? '') . ' - ' . ($inventaris->nama_barang ?? ''),
            $inventaris->id,
            $inventaris->nama_barang ?? $inventaris->kode_barang
        );

        // DISABLED: approval system disabled - no pending notifications
        // Notifikasi ke super_admin & sekretaris_desa bahwa ada data baru menunggu persetujuan
        /*
        if ($inventaris->status === 'pending') {
            $namaData   = $inventaris->display_name ?? $inventaris->nama_barang ?? $inventaris->kode_barang ?? 'Data baru';
            $jenisNama  = $inventaris->jenis->nama ?? 'Inventaris';
            $namaAktor  = auth()->user()->nama ?? auth()->user()->name ?? 'Seseorang';
            Notification::sendToRoles(
                ['super_admin', 'sekretaris_desa'],
                'inventaris_pending',
                'Data baru menunggu persetujuan',
                "$namaAktor menambahkan $namaData ($jenisNama) dan menunggu persetujuan Anda.",
                ['inventaris_id' => $inventaris->id, 'actor_id' => auth()->id()],
                route('persetujuan.index')
            );
        }
        */

        \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
        \Illuminate\Support\Facades\Cache::forget('dashboard_charts');

        // Jika copy_jumlah > 1, duplikasi aset yang baru dibuat
        $copyJumlah = (int) $request->input('copy_jumlah', 1);
        if ($copyJumlah > 1) {
            $copyJumlah = min(50, max(1, $copyJumlah));
            $copyRequest = new \Illuminate\Http\Request();
            $copyRequest->merge(['jumlah' => $copyJumlah - 1]); // -1 karena 1 sudah tersimpan
            $this->duplicate($copyRequest, $inventaris->id);
            $namaDisplay = $inventaris->nama_barang ?? $inventaris->kode_barang ?? 'Aset';
            return redirect()->route('manajemen-aset.index')->with('success', "{$copyJumlah} salinan {$namaDisplay} berhasil dibuat.");
        }

        $namaDisplay = $inventaris->nama_barang ?? $inventaris->kode_barang ?? 'Inventaris baru';
        return redirect()->route('manajemen-aset.index')->with('success', "Data Aset {$namaDisplay} berhasil ditambahkan.");
    }

    public function show($id)
    {
        $inventaris = Inventaris::with([
            'jenis.fields' => fn($q) => $q->orderBy('order', 'asc'),
            'values.field',
            'creator',
            'updater',
            'fotos',
            'ruangan',
            'kibValues',
            'kirValues',
        ])->findOrFail($id);
        $this->authorize('view', $inventaris);
        
        // Use dynamic QR endpoint to avoid filesystem write issues on hosting
        $qrUrl = null;
        if (Schema::hasColumn('inventaris', 'barcode') && $inventaris->barcode) {
            $qrUrl = route('inventaris.qr', ['id' => $inventaris->id]);
        }

        // KIB config untuk tampilan detail
        $kibConfig = $inventaris->jenis?->kib_type
            ? config('kib.' . $inventaris->jenis->kib_type, [])
            : [];

        return view('manajemen-aset.show', compact('inventaris', 'qrUrl', 'kibConfig'));
    }

    public function edit($id)
    {
        $inventaris = Inventaris::with(['jenis', 'values.field', 'fotos', 'kirValues'])->findOrFail($id);
        $this->authorize('update', $inventaris);
        
        // Exclude Buku Inventaris Aset Desa (virtual jenis tidak untuk input data individual)
        $jenisList = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
                                     ->with('fields')
                                     ->orderBy('nama')
                                     ->get();
        
        // Load all fields untuk jenis ini, urut by order
        $allFields = $inventaris->jenis
            ? $inventaris->jenis->fields()->orderBy('order', 'asc')->get()
            : collect();
        
        // Map fields dengan data - gunakan field_type dari database
        $fieldsWithData = $allFields->map(function($field) use ($inventaris) {
            if ($field->field_type === 'fixed') {
                // Fixed field: ambil dari inventaris table
                return [
                    'field' => $field,
                    'value' => $inventaris->{$field->nama_field} ?? '',
                    'type' => 'fixed'
                ];
            } else {
                // Dynamic field: ambil dari inventaris_values
                $fieldValue = $inventaris->values()
                    ->where('jenis_inventaris_data_id', $field->id)
                    ->first();
                
                return [
                    'field' => $field,
                    'value' => $fieldValue?->value ?? '',
                    'type' => 'dynamic'
                ];
            }
        });

        // KIB values dan config untuk section Data KIB
        $kibValues = $inventaris->kibValues()->pluck('value', 'kib_field_key')->toArray();
        $kibConfig = $inventaris->jenis?->kib_type
            ? config('kib.' . $inventaris->jenis->kib_type, [])
            : [];
        
        return view('manajemen-aset.edit', [
            'inventaris' => $inventaris,
            'jenisList' => $jenisList,
            'fieldsWithData' => $fieldsWithData,
            'userList' => \App\Models\User::excludeSuperDevelopers()->where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']),
            'ruanganList' => \App\Models\Ruangan::orderBy('nama')->get(['id', 'nama', 'kode' /*, 'lokasi'*/]),
            'kibValues' => $kibValues,
            'kibConfig' => $kibConfig,
        ]);
    }

    /**
    /**
     * Konfigurasi root URL untuk QR Code / Barcode agar kompatibel dengan akses jaringan lokal (LAN) maupun domain.
     */
    protected function configureQrRootUrl(): void
    {
        $appUrl = config('app.url') ?: env('APP_URL');
        $currentRoot = (!app()->runningInConsole() && request()->hasHeader('Host')) ? request()->root() : null;
        $rootToUse = $currentRoot ?: $appUrl;

        if ($rootToUse) {
            \Illuminate\Support\Facades\URL::forceRootUrl(rtrim($rootToUse, '/'));
            $scheme = parse_url($rootToUse, PHP_URL_SCHEME);
            if ($scheme) {
                \Illuminate\Support\Facades\URL::forceScheme($scheme);
            }
        }
    }

    /**
     * Serve QR PNG dynamically to avoid storage permissions issues.
     */
    public function qrImage($id)
    {
        $inventaris = Inventaris::select('id', 'barcode')->findOrFail($id);
        $this->authorize('view', $inventaris);

        if (!Schema::hasColumn('inventaris', 'barcode') || empty($inventaris->barcode)) {
            abort(404);
        }

        $this->configureQrRootUrl();

        $qrData = \Illuminate\Support\Facades\URL::signedRoute('inventaris.scan', ['barcode' => $inventaris->barcode]);
        if (!preg_match('#^https?://#i', $qrData)) {
            $qrData = 'http://' . ltrim($qrData, '/');
        }

        $qrCode = QrCode::create($qrData)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function update(UpdateInventarisRequest $request, $id)
    {
        $data = $request->validated();
        $dynamicFields = $request->input('fields', []); // Array dengan key = field_id
        $fotoWarning = null;

        $inventaris = Inventaris::findOrFail($id);
        $this->authorize('update', $inventaris);

        /* Log::debug('Inventaris.update', [
            'inventaris_id' => $id,
            'user_id' => auth()->id(),
            'kode_barang' => $data['kode_barang'] ?? null,
            'has_fotos' => $request->hasFile('fotos'),
            'fotos_count' => is_array($request->file('fotos')) ? count($request->file('fotos')) : ($request->file('fotos') ? 1 : 0),
            'all_files_keys' => array_keys($request->allFiles()),
        ]); */

        DB::beginTransaction();
        try {
            // Update fixed fields dari inventaris table
            $inventaris->update([
                'jenis_inventaris_id' => $data['jenis_inventaris_id'],
                'jenis_barang' => $data['jenis_barang'] ?? null,
                'nama_barang' => $data['nama_barang'] ?? null,
                'satuan' => $data['satuan'] ?? null,
                'kode_barang' => $data['kode_barang'] ?? $inventaris->kode_barang,
                'nup' => $data['nup'] ?? null,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'asal_usul' => $data['asal_usul'] ?? null,
                'bukti_kepemilikan' => $data['bukti_kepemilikan'] ?? null,
                'pengguna' => $data['pengguna'] ?? null,
                'ruangan_id' => $data['ruangan_id'] ?? null,
                'kondisi' => array_key_exists('kondisi', $data) ? $data['kondisi'] : ($inventaris->kondisi ?? 'baik'),
                // Status NOT changed via form - only via approve() method
                // updated_by di-set otomatis oleh Inventaris::booted()
            ]);

            // Update dynamic fields dari inventaris_values
            if (!empty($dynamicFields) && is_array($dynamicFields)) {
                // Keamanan: hanya izinkan field ID yang benar-benar milik jenis inventaris ini.
                // Mencegah seseorang inject field ID dari jenis lain â†’ data corrupt.
                $validFieldIds = JenisInventarisData::where('jenis_inventaris_id', $data['jenis_inventaris_id'])
                    ->pluck('id')
                    ->map(fn($id) => (string) $id)
                    ->toArray();

                $dynamicFields = array_filter(
                    $dynamicFields,
                    fn($key) => in_array((string) $key, $validFieldIds, true),
                    ARRAY_FILTER_USE_KEY
                );

                // $dynamicFields format: [field_id => value, field_id => value, ...]
                foreach ($dynamicFields as $fieldId => $value) {
                    InventarisValue::updateOrCreate(
                        [
                            'inventaris_id' => $inventaris->id,
                            'jenis_inventaris_data_id' => $fieldId,
                        ],
                        ['value' => $value]
                    );
                }
            }

            // Update KIB values (field statis Buku Inventaris Aset Desa)
            $kibData = $request->input('kib', []);
            if (is_array($kibData)) {
                foreach ($kibData as $key => $value) {
                    if ($value === null || trim((string)$value) === '') {
                        \App\Models\InventarisKibValue::where('inventaris_id', $inventaris->id)
                            ->where('kib_field_key', $key)->delete();
                        continue;
                    }
                    \App\Models\InventarisKibValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kib_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Update KIR values
            $kirData = $request->input('kir', []);
            if (is_array($kirData)) {
                foreach ($kirData as $key => $value) {
                    if ($value === null || trim((string)$value) === '') {
                        \App\Models\InventarisKirValue::where('inventaris_id', $inventaris->id)
                            ->where('kir_field_key', $key)->delete();
                        continue;
                    }
                    \App\Models\InventarisKirValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kir_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Update Laporan Aset values (bulan perolehan)
            // Hanya update jika nilai dikirim dari form (tidak kosong/null).
            // Kalau kosong → biarkan nilai lama di DB tetap, tidak disentuh.
            $bulanPerolehan = $request->input('bulan_perolehan');
            if ($bulanPerolehan !== null && $bulanPerolehan !== '') {
                \App\Models\InventarisLaporanAsetValue::updateOrCreate(
                    ['inventaris_id' => $inventaris->id],
                    ['bulan_perolehan' => (int)$bulanPerolehan]
                );
            }

            DB::commit();

            // Handle foto deletions & uploads (outside DB transaction â€” storage only)
            try {
                if ($request->filled('delete_fotos')) {
                    $toDelete = InventarisFoto::whereIn('id', (array) $request->input('delete_fotos'))
                        ->where('inventaris_id', $inventaris->id)
                        ->get();
                    foreach ($toDelete as $foto) {
                        Storage::disk('public')->delete($foto->path);
                        $foto->delete();
                    }
                }

                if ($request->hasFile('fotos')) {
                    $existingCount = $inventaris->fotos()->count();
                    $slots = max(0, 5 - $existingCount);
                    $fotoFiles = array_slice((array) $request->file('fotos'), 0, $slots);

                    /* Log::debug('Inventaris.update.foto_upload_start', [
                        'inventaris_id' => $inventaris->id,
                        'existing_count' => $existingCount,
                        'slots' => $slots,
                        'incoming_count' => count($fotoFiles),
                    ]); */

                    foreach ($fotoFiles as $i => $foto) {
                        $path = $foto->store('inventaris-foto/' . $inventaris->id, 'public');
                        InventarisFoto::create([
                            'inventaris_id' => $inventaris->id,
                            'path'          => $path,
                            'urutan'        => $existingCount + $i,
                        ]);
                    }

                    /* Log::debug('Inventaris.update.foto_upload_done', [
                        'inventaris_id' => $inventaris->id,
                        'saved_count' => count($fotoFiles),
                    ]); */
                }
            } catch (\Throwable $fotoException) {
                Log::error('Foto upload/delete error after inventaris update', [
                    'inventaris_id' => $inventaris->id,
                    'error' => $fotoException->getMessage(),
                    'has_fotos' => $request->hasFile('fotos'),
                    'fotos_count' => is_array($request->file('fotos')) ? count($request->file('fotos')) : ($request->file('fotos') ? 1 : 0),
                ]);
                $fotoWarning = 'Perubahan data utama tersimpan, tetapi proses foto gagal. Silakan coba unggah ulang foto.';
            }

            Log::info('Inventaris updated', [
                'inventaris_id' => $inventaris->id,
                'updated_by' => $inventaris->updated_by,
                'dynamic_fields' => array_keys($dynamicFields),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Inventaris update failed', [
                'inventaris_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => array_merge($data, ['fields' => array_keys($dynamicFields)]),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Gagal memperbarui inventaris. Silakan coba lagi.')->withInput();
        }

        ActivityLogger::log('Inventaris', 'ubah',
            'Mengubah inventaris ' . ($inventaris->kode_barang ?? '') . ' - ' . ($inventaris->nama_barang ?? ''),
            $inventaris->id,
            $inventaris->nama_barang ?? $inventaris->kode_barang
        );

        \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
        \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
        $redirect = redirect()->route('manajemen-aset.index')->with('success', "Data Aset {$inventaris->nama_barang} berhasil diperbarui.");
        if ($fotoWarning) {
            $redirect->with('warning', $fotoWarning);
        }

        return $redirect;
    }

    public function destroy(Request $request, $id)
    {
        $inventaris = Inventaris::find($id);
        if (!$inventaris) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Inventaris tidak ditemukan.'], 404);
            }
            return redirect()->route('manajemen-aset.index')->with('error', 'Inventaris tidak ditemukan.');
        }
        if (!auth()->user()->canDeleteAset()) {
            abort(403, 'Hanya Kaur Umum, Sekretaris Desa, Perbekel, dan Super Admin yang berhak menghapus aset.');
        }
        $this->authorize('delete', $inventaris);
        
        $tipeHapus = $request->input('tipe_hapus', 'salah_input');

        if ($tipeHapus === 'mutasi_berkurang') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penghapusan resmi aset wajib diproses melalui modul Penghapusan Aset dengan melampirkan Dokumen Berita Acara / SK.',
                    'redirect_url' => route('penghapusan-aset.index', ['inventaris_id' => $id])
                ], 422);
            }
            return redirect()->route('penghapusan-aset.index', ['inventaris_id' => $id])
                ->with('info', 'Penghapusan resmi aset wajib diproses melalui modul Penghapusan Aset dengan melampirkan Dokumen Berita Acara / SK.');
        }
        
        try {
            DB::beginTransaction();

            // Cascade delete manual - Hapus semua relasi aset
            $valuesCount = InventarisValue::where('inventaris_id', $id)->count();
            if ($valuesCount > 0) {
                InventarisValue::where('inventaris_id', $id)->delete();
            }

            \App\Models\InventarisKibValue::where('inventaris_id', $id)->delete();
            \App\Models\InventarisKirValue::where('inventaris_id', $id)->delete();
            \App\Models\InventarisLaporanAsetValue::where('inventaris_id', $id)->delete();

            $exportLogsCount = \App\Models\ExportLog::where('inventaris_id', $id)->count();
            if ($exportLogsCount > 0) {
                \App\Models\ExportLog::where('inventaris_id', $id)->update(['inventaris_id' => null]);
            }

            // Delete foto files from storage
            foreach ($inventaris->fotos as $foto) {
                Storage::disk('public')->delete($foto->path);
            }
            InventarisFoto::where('inventaris_id', $id)->delete();

            // Delete barcode PNG if exists
            $barcodePath = public_path('barcodes/barcode_' . $id . '.png');
            if (file_exists($barcodePath)) {
                @unlink($barcodePath);
            }

            $kodeBarang = $inventaris->kode_barang;
            $namaBarang = $inventaris->nama_barang;

            $inventaris->delete();

            DB::commit();

            ActivityLogger::log('Inventaris', 'hapus',
                "Menghapus inventaris {$kodeBarang} - {$namaBarang} (Salah Input)",
                $id, $namaBarang
            );

            Log::info('Inventaris deleted successfully with cascade', [
                'inventaris_id'         => $id,
                'kode_barang'           => $kodeBarang,
                'nama_barang'           => $namaBarang,
                'tipe_hapus'            => $tipeHapus,
                'values_deleted'        => $valuesCount,
                'export_logs_nullified' => $exportLogsCount,
                'deleted_by'            => auth()->id(),
            ]);
            
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
            $namaDisplay = $namaBarang ?? $kodeBarang ?? 'Inventaris';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Aset {$namaDisplay} berhasil dihapus."
                ]);
            }
            
            return redirect()->route('manajemen-aset.index')->with('success', "Data Aset {$namaDisplay} berhasil dihapus.");
                
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Inventaris delete failed', [
                'inventaris_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            $namaDisplay = $inventaris->nama_barang ?? $inventaris->kode_barang ?? 'Inventaris';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal menghapus Aset {$namaDisplay}."
                ], 500);
            }
            
            return redirect()->route('manajemen-aset.index')->with('error', 
                "Gagal menghapus Aset {$namaDisplay}. Silakan coba lagi.");
        }
    }

    // The following routes and methods were disabled:
    // - approve()
    // - resubmit()
    // - bulkApprove()

    /**
     * Maintenance: regenerate barcode/QR PNG without terminal access.
     * Usage (super admin only): /maintenance/qr-regenerate?only_missing=1&limit=200&after_id=0
     */
    public function regenerateQr(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->isSuperDeveloper()), 403);

        // Configure URL root and scheme for QR Code
        $this->configureQrRootUrl();

        if (!Schema::hasColumn('inventaris', 'barcode')) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom barcode tidak ditemukan di tabel inventaris.',
            ], 500);
        }

        $limit = (int) $request->query('limit', 200);
        $limit = max(1, min($limit, 2000));
        $afterId = (int) $request->query('after_id', 0);
        $onlyMissing = $request->boolean('only_missing', true);

        $dir = public_path('barcodes');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }

        $stats = [
            'processed' => 0,
            'barcodes_created' => 0,
            'png_created' => 0,
            'png_failed' => 0,
            'skipped' => 0,
        ];
        $errors = [];
        $lastId = $afterId;

        $query = Inventaris::query()
            ->select('id', 'barcode')
            ->orderBy('id');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $query->chunkById(200, function ($items) use (&$stats, &$errors, &$lastId, $dir, $limit, $onlyMissing) {
            foreach ($items as $item) {
                if ($stats['processed'] >= $limit) {
                    return false;
                }

                $stats['processed']++;
                $lastId = $item->id;

                $barcode = (string) ($item->barcode ?? '');
                $hasBarcode = $barcode !== '';
                $pngPath = $dir . DIRECTORY_SEPARATOR . 'barcode_' . $item->id . '.png';
                $pngMissing = !file_exists($pngPath);

                if ($onlyMissing && $hasBarcode && !$pngMissing) {
                    $stats['skipped']++;
                    continue;
                }

                if (!$hasBarcode) {
                    $barcode = $this->generateUniqueBarcode();
                    $item->barcode = $barcode;
                    $item->save();
                    $stats['barcodes_created']++;
                }

                if ($pngMissing || !$onlyMissing) {
                    try {
                        $this->generateQrPng($item->id, $barcode);
                        $stats['png_created']++;
                    } catch (\Throwable $e) {
                        $stats['png_failed']++;
                        if (count($errors) < 5) {
                            $errors[] = 'ID ' . $item->id . ': ' . $e->getMessage();
                        }
                    }
                }
            }

            return true;
        });

        return response()->json([
            'success' => true,
            'message' => 'QR regeneration completed.',
            'only_missing' => $onlyMissing,
            'limit' => $limit,
            'after_id' => $afterId,
            'last_id' => $lastId,
            'stats' => $stats,
            'errors' => $errors,
        ]);
    }

    /**
     * Handle QR code scan: lookup inventaris by barcode string and redirect to detail page.
     * Route: GET /scan/{barcode}
     */
    public function scan($barcode)
    {
        $inventaris = Inventaris::with(['jenis', 'ruangan.penanggungJawab.role', 'ruangan.penanggungJawab.pejabat', 'values.field', 'fotos'])
            ->where('barcode', $barcode)
            ->first();

        $qrUrl = null;
        if ($inventaris) {
            $qrPath = public_path('barcodes/barcode_' . $inventaris->id . '.png');
            if (file_exists($qrPath)) {
                $qrUrl = asset('barcodes/barcode_' . $inventaris->id . '.png');
            }
        }

        return view('scan.show', compact('inventaris', 'qrUrl'));
    }

    // Helpers
    private function generateUniqueBarcode()
    {
        return Inventaris::generateBarcode();
    }

    private function generateQrPng($inventarisId, $barcode)
    {
        $dir = public_path('barcodes');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "barcode_{$inventarisId}.png";
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        // Configure root URL for QR Code
        $this->configureQrRootUrl();

        // Embed scan URL so scanning opens the public detail page directly
        $qrData = \Illuminate\Support\Facades\URL::signedRoute('inventaris.scan', ['barcode' => $barcode]);
        // Guarantee scheme present (some QR readers/platforms strip or present schemeless text)
        if (!preg_match('#^https?://#i', $qrData)) {
            $qrData = 'http://' . ltrim($qrData, '/');
        }

        $qrCode = QrCode::create($qrData)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $result->saveToFile($path);

        return asset('barcodes/' . $filename);
    }

    public function deleteFoto($id)
    {
        $foto = InventarisFoto::findOrFail($id);
        if ($foto->inventaris) {
            $this->authorize('update', $foto->inventaris);
        } else {
            $this->authorize('delete', Inventaris::class);
        }

        Storage::disk('public')->delete($foto->path);
        $foto->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Duplikasi aset sebanyak N kali.
     * POST /manajemen-aset/{id}/duplicate
     */
    public function duplicate(Request $request, $id)
    {
        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $source = Inventaris::with(['values', 'fotos', 'kibValues', 'kirValues', 'laporanAsetValue'])->findOrFail($id);
        $this->authorize('create', Inventaris::class);

        $jumlah = (int) $request->input('jumlah', 1);
        $created = 0;

        // Auto-increment NUP berurutan berdasarkan NUP numerik tertinggi untuk kode barang ini (lintas tahun sesuai standar runtut SIMA)
        $nupQuery = Inventaris::where('kode_barang', $source->kode_barang);
        $existingNups = $nupQuery
            ->pluck('nup')
            ->map(function ($val) {
                return is_numeric($val) ? (int)$val : 0;
            });
        $maxNup = $existingNups->max() ?: 0;

        DB::beginTransaction();
        try {
            $candidateNup = $maxNup;
            $createdNups = [];
            for ($i = 0; $i < $jumlah; $i++) {
                $candidateNup++;
                while (Inventaris::where('kode_barang', $source->kode_barang)->where('nup', (string)$candidateNup)->exists()) {
                    $candidateNup++;
                }

                // Copy data utama — barcode baru, NUP auto-increment
                $newData = $source->only([
                    'jenis_inventaris_id', 'jenis_barang', 'nama_barang', 'satuan',
                    'kode_barang', 'tahun_perolehan', 'nilai_perolehan',
                    'keterangan', 'asal_usul', 'bukti_kepemilikan', 'pengguna', 'kondisi', 'ruangan_id',
                ]);
                $newData['nup']     = (string)$candidateNup;
                $newData['status']  = 'draft';
                $newData['barcode'] = $this->generateUniqueBarcode();

                $newInventaris = Inventaris::create($newData);
                $createdNups[] = $candidateNup;

                // Copy dynamic values
                foreach ($source->values as $val) {
                    InventarisValue::create([
                        'inventaris_id'           => $newInventaris->id,
                        'jenis_inventaris_data_id' => $val->jenis_inventaris_data_id,
                        'value'                   => $val->value,
                    ]);
                }

                // Copy KIB values
                foreach ($source->kibValues as $kib) {
                    \App\Models\InventarisKibValue::create([
                        'inventaris_id' => $newInventaris->id,
                        'kib_field_key' => $kib->kib_field_key,
                        'value'         => $kib->value,
                    ]);
                }

                // Copy KIR values
                foreach ($source->kirValues as $kir) {
                    \App\Models\InventarisKirValue::create([
                        'inventaris_id' => $newInventaris->id,
                        'kir_field_key' => $kir->kir_field_key,
                        'value'         => $kir->value,
                    ]);
                }

                // Copy Laporan Aset values (bulan perolehan)
                if ($source->laporanAsetValue) {
                    \App\Models\InventarisLaporanAsetValue::create([
                        'inventaris_id'   => $newInventaris->id,
                        'bulan_perolehan' => $source->laporanAsetValue->bulan_perolehan,
                    ]);
                }

                // Copy foto — salin file fisik ke path baru
                foreach ($source->fotos as $urutan => $foto) {
                    $ext      = pathinfo($foto->path, PATHINFO_EXTENSION);
                    $newPath  = 'inventaris-foto/' . $newInventaris->id . '/' . uniqid() . '.' . $ext;
                    if (Storage::disk('public')->exists($foto->path)) {
                        Storage::disk('public')->copy($foto->path, $newPath);
                        InventarisFoto::create([
                            'inventaris_id' => $newInventaris->id,
                            'path'          => $newPath,
                            'urutan'        => $foto->urutan,
                        ]);
                    }
                }

                // Generate QR
                try {
                    $this->generateQrPng($newInventaris->id, $newInventaris->barcode);
                } catch (\Throwable $e) {
                    Log::warning('Duplicate: gagal generate QR', ['id' => $newInventaris->id]);
                }

                $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Duplicate inventaris failed', ['source_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal menduplikasi aset. Silakan coba lagi.'], 500);
        }

        $nupRangeText = count($createdNups) === 1
            ? "NUP " . $createdNups[0]
            : "NUP " . min($createdNups) . " s.d. " . max($createdNups);

        $namaDisplay = $source->nama_barang ?? $source->kode_barang ?? 'Aset';
        ActivityLogger::log('Inventaris', 'tambah',
            "Menduplikasi aset '{$namaDisplay}' sebanyak {$created} salinan ({$nupRangeText})",
            $source->id, $namaDisplay
        );
        \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
        \Illuminate\Support\Facades\Cache::forget('dashboard_charts');

        $msg = $created === 1
            ? "Data Aset {$namaDisplay} berhasil diduplikasi ({$nupRangeText})."
            : "{$created} salinan {$namaDisplay} berhasil dibuat ({$nupRangeText}).";

        // Kalau AJAX (dari index), return JSON
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => $msg, 'created' => $created]);
        }

        return redirect()->route('manajemen-aset.index')->with('success', $msg);
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->canDeleteAset()) {
            abort(403, 'Hanya Kaur Umum, Sekretaris Desa, Perbekel, dan Super Admin yang berhak menghapus aset.');
        }
        $this->authorize('delete', Inventaris::class);
        $ids = $request->input('ids', []);
        $tipeHapus = $request->input('tipe_hapus', 'salah_input');
        if (empty($ids)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada aset yang dipilih.'], 422);
            }
            return redirect()->back()->with('error', 'Tidak ada aset yang dipilih.');
        }

        if ($tipeHapus === 'mutasi_berkurang') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penghapusan resmi aset wajib diproses melalui modul Penghapusan Aset dengan melampirkan Dokumen Berita Acara / SK.',
                    'redirect_url' => route('penghapusan-aset.index', ['inventaris_ids' => implode(',', $ids)])
                ], 422);
            }
            return redirect()->route('penghapusan-aset.index', ['inventaris_ids' => implode(',', $ids)]);
        }

        $inventarisList = Inventaris::with('fotos')->whereIn('id', $ids)->get();
        if ($inventarisList->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Aset yang dipilih tidak ditemukan atau sudah dihapus.'], 404);
            }
            return redirect()->back()->with('error', 'Aset yang dipilih tidak ditemukan atau sudah dihapus.');
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($inventarisList as $item) {
                InventarisValue::where('inventaris_id', $item->id)->delete();
                \App\Models\InventarisKibValue::where('inventaris_id', $item->id)->delete();
                \App\Models\InventarisKirValue::where('inventaris_id', $item->id)->delete();
                \App\Models\InventarisLaporanAsetValue::where('inventaris_id', $item->id)->delete();
                \App\Models\ExportLog::where('inventaris_id', $item->id)->update(['inventaris_id' => null]);
                foreach ($item->fotos as $foto) {
                    Storage::disk('public')->delete($foto->path);
                }
                InventarisFoto::where('inventaris_id', $item->id)->delete();
                $barcodePath = public_path('barcodes/barcode_' . $item->id . '.png');
                if (file_exists($barcodePath)) {
                    @unlink($barcodePath);
                }
                $item->delete();
                $count++;
            }
            DB::commit();

            ActivityLogger::log(
                'Inventaris',
                'hapus',
                "Menghapus {$count} aset inventaris sekaligus (tipe: " . ($tipeHapus === 'mutasi_berkurang' ? 'Mutasi Berkurang' : 'Salah Input') . ")",
                null,
                "{$count} Aset"
            );

            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => "{$count} aset berhasil dihapus."]);
            }
            return redirect()->route('manajemen-aset.index')->with('success', "{$count} aset berhasil dihapus.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ManajemenAsetController::bulkDestroy error', ['error' => $e->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function getNextNup(Request $request)
    {
        $kodeBarang = $request->query('kode_barang');
        $tahunPerolehan = $request->query('tahun_perolehan') ?? $request->query('tahun');
        $ignoreId = $request->query('ignore_id');

        if (!$kodeBarang) {
            return response()->json([
                'next_nup'        => 1,
                'max_nup'         => 0,
                'existing_nups'   => [],
                'tahun_perolehan' => $tahunPerolehan,
            ]);
        }

        // NUP berurutan lintas tahun per kode barang (sesuai standar runtut SIMA)
        $query = Inventaris::where('kode_barang', $kodeBarang);
        if (!empty($ignoreId)) {
            $query->where('id', '!=', (int)$ignoreId);
        }

        $existingNups = $query
            ->pluck('nup')
            ->map(function ($val) {
                return is_numeric($val) ? (int)$val : 0;
            })
            ->filter(fn($v) => $v > 0)
            ->values();

        $maxNup = $existingNups->max() ?: 0;
        $nextNup = $maxNup + 1;

        return response()->json([
            'kode_barang'     => $kodeBarang,
            'tahun_perolehan' => $tahunPerolehan,
            'max_nup'         => $maxNup,
            'next_nup'        => $nextNup,
            'existing_nups'   => $existingNups,
        ]);
    }

    /**
     * Cetak label stiker QR Code masal untuk aset yang dipilih.
     */
    public function bulkPrintQr(Request $request)
    {
        $this->authorize('viewAny', Inventaris::class);

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter(array_map('intval', (array) $ids));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu aset untuk mencetak label QR.');
        }

        // Batasi maksimal 100 stiker per cetak untuk menjaga performa rendering DomPDF
        $ids = array_slice($ids, 0, 100);

        $format = $request->input('format', 'sticker');
        if (!in_array($format, ['sticker', 'plain'])) {
            $format = 'sticker';
        }

        $inventarisList = Inventaris::with(['ruangan', 'jenis'])
            ->whereIn('id', $ids)
            ->get();

        if ($inventarisList->isEmpty()) {
            return redirect()->back()->with('error', 'Data aset tidak ditemukan.');
        }

        // Configure root URL for QR Code
        $this->configureQrRootUrl();

        $writer = new PngWriter();
        $itemsWithQr = [];

        foreach ($inventarisList as $item) {
            if (empty($item->barcode)) {
                $item->barcode = Inventaris::generateBarcode();
                $item->saveQuietly();
            }

            $qrData = \Illuminate\Support\Facades\URL::signedRoute('inventaris.scan', ['barcode' => $item->barcode]);
            if (!preg_match('#^https?://#i', $qrData)) {
                $qrData = 'http://' . ltrim($qrData, '/');
            }

            $qrCode = QrCode::create($qrData)
                ->setSize(300)
                ->setMargin(2);

            $result = $writer->write($qrCode);
            $base64 = 'data:image/png;base64,' . base64_encode($result->getString());

            $itemsWithQr[] = [
                'item' => $item,
                'qrBase64' => $base64,
            ];
        }

        $logoPath = public_path('assets/images/logo/logo-sima-desa-demo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        ActivityLogger::log('Inventaris', 'export', 'Mencetak ' . count($itemsWithQr) . ' label stiker QR code aset (' . $format . ')');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('manajemen-aset.bulk-qr-pdf', [
            'items' => $itemsWithQr,
            'logoBase64' => $logoBase64,
            'format' => $format,
        ])->setPaper('a4', 'portrait');

        $filePrefix = $format === 'plain' ? 'QR_Code_Polos_' : 'Label_Stiker_Resmi_';
        return $pdf->stream($filePrefix . date('Ymd_His') . '.pdf');
    }
}

