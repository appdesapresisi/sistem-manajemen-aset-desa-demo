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
        $query = Inventaris::with(['jenis', 'values.field']);
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%')
                  ->orWhere('nup', 'like', '%' . $search . '%')
                  ->orWhereHas('jenis', function($jq) use ($search) {
                      $jq->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Jenis filter
        if ($request->has('jenis') && is_array($request->jenis) && count($request->jenis) > 0) {
            $query->whereIn('jenis_inventaris_id', $request->jenis);
        }
        
        // Status filter — DISABLED: approval system removed, tidak ada filter status
        // if ($request->has('status') && $request->status !== '' && $request->status !== 'all') {
        //     $query->where('status', $request->status);
        // }
        
        // Sort order
        $sortBy = $request->get('sort', 'nama_asc');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama_barang', 'desc');
        } elseif ($sortBy === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('nama_barang', 'asc');
        }
        
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        // Paginate with items per page
        $inventarisPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
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

        // Get total counts with status breakdown & trends
        $stats = [
            'totalInventaris'    => $totalInv,
            'kondisiBaik'        => $baikTotal,
            'kondisiRusakRingan' => $rrTotal,
            'kondisiRusakBerat'  => $rbTotal,
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
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            $items = collect($inventarisPaginated->items())->map(function($item) {
                $jenis = $item->jenis;
                $colors = $jenis ? ($jenis->color_gradient ?? ['#6b7280','#4b5563']) : ['#6b7280','#4b5563'];
                return [
                    'id'           => $item->id,
                    'display_name' => $item->display_name ?? '-',
                    'nup'          => $item->nup ?? '-',
                    'kode_barang'  => $item->kode_barang,
                    'jenis_nama'   => $jenis->nama ?? 'Unknown',
                    'jenis_icon'   => $jenis ? ($jenis->icon ?? 'box') : 'box',
                    'jenis_color0' => $colors[0],
                    'jenis_color1' => $colors[1],
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
            'inventaris' => $inventarisPaginated,
            'stats' => $stats,
            'jenisList' => $jenisList,
        ]);
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
        $userList = \App\Models\User::where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);

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
        $allFields = $inventaris->jenis->fields()
            ->orderBy('order', 'asc')
            ->get();
        
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
            'userList' => \App\Models\User::where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']),
            'ruanganList' => \App\Models\Ruangan::orderBy('nama')->get(['id', 'nama', 'kode' /*, 'lokasi'*/]),
            'kibValues' => $kibValues,
            'kibConfig' => $kibConfig,
        ]);
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

        $appUrl = config('app.url') ?: env('APP_URL');
        if ($appUrl) {
            \Illuminate\Support\Facades\URL::forceRootUrl(rtrim($appUrl, '/'));
            $scheme = parse_url($appUrl, PHP_URL_SCHEME);
            if ($scheme) {
                \Illuminate\Support\Facades\URL::forceScheme($scheme);
            }
        }

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
                'pengguna' => $data['pengguna'] ?? null,
                'ruangan_id' => $data['ruangan_id'] ?? null,
                'kondisi' => $data['kondisi'] ?? null,
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

    public function destroy($id)
    {
        $inventaris = Inventaris::find($id);
        if (!$inventaris) {
            return redirect()->route('manajemen-aset.index')->with('error', 'Inventaris tidak ditemukan.');
        }
        $this->authorize('delete', $inventaris);
        
        try {
            DB::beginTransaction();

            // Cascade delete manual - Hapus semua relasi
            $valuesCount = InventarisValue::where('inventaris_id', $id)->count();
            if ($valuesCount > 0) {
                InventarisValue::where('inventaris_id', $id)->delete();
            }

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

            // Log ke inventaris_deleted_log untuk tracking mutasi berkurang
            \App\Models\InventarisDeletedLog::create([
                'kode_barang'     => $inventaris->kode_barang,
                'nup'             => $inventaris->nup,
                'nama_barang'     => $inventaris->nama_barang,
                'satuan'          => $inventaris->satuan,
                'nilai_perolehan' => $inventaris->nilai_perolehan,
                'tahun_perolehan' => $inventaris->tahun_perolehan,
                'deleted_at'      => now(),
                'deleted_by'      => auth()->id(),
            ]);

            $inventaris->delete();

            DB::commit();

            ActivityLogger::log('Inventaris', 'hapus',
                "Menghapus inventaris {$kodeBarang} - {$namaBarang}",
                $id, $namaBarang
            );

            Log::info('Inventaris deleted successfully with cascade', [
                'inventaris_id'         => $id,
                'kode_barang'           => $kodeBarang,
                'nama_barang'           => $namaBarang,
                'values_deleted'        => $valuesCount,
                'export_logs_nullified' => $exportLogsCount,
                'deleted_by'            => auth()->id(),
            ]);
            
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
            $namaDisplay = $namaBarang ?? $kodeBarang ?? 'Inventaris';
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
        abort_unless($user && $user->isSuperAdmin(), 403);

        // Force URL root and scheme from APP_URL to avoid generation using a different host
        $appUrl = config('app.url') ?: env('APP_URL');
        if ($appUrl) {
            \Illuminate\Support\Facades\URL::forceRootUrl(rtrim($appUrl, '/'));
            $scheme = parse_url($appUrl, PHP_URL_SCHEME);
            if ($scheme) {
                \Illuminate\Support\Facades\URL::forceScheme($scheme);
            }
        }

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
        $inventaris = Inventaris::with(['jenis', 'values.field', 'fotos'])
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
        $attempt = 0;
        do {
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));
            $exists = Inventaris::where('barcode', $candidate)->exists();
            $attempt++;
        } while ($exists && $attempt < 10);

        if ($exists) {
            // Fallback: gunakan UUID yang dijamin unik secara matematis
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
            // Pastikan fallback juga tidak bentrok
            if (Inventaris::where('barcode', $candidate)->exists()) {
                $candidate = 'INV-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
            }
        }

        return $candidate;
    }

    private function generateQrPng($inventarisId, $barcode)
    {
        $dir = public_path('barcodes');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "barcode_{$inventarisId}.png";
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        // Ensure signed URLs use APP_URL rather than the current request host
        $appUrl = config('app.url') ?: env('APP_URL');
        if ($appUrl) {
            \Illuminate\Support\Facades\URL::forceRootUrl(rtrim($appUrl, '/'));
            $scheme = parse_url($appUrl, PHP_URL_SCHEME);
            if ($scheme) {
                \Illuminate\Support\Facades\URL::forceScheme($scheme);
            }
        }

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
        $this->authorize('update', $foto->inventaris);

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

        $source = Inventaris::with(['values', 'fotos', 'kibValues', 'kirValues'])->findOrFail($id);
        $this->authorize('create', Inventaris::class);

        $jumlah = (int) $request->input('jumlah', 1);
        $created = 0;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $jumlah; $i++) {
                // Copy data utama — barcode baru, sisanya identik
                $newData = $source->only([
                    'jenis_inventaris_id', 'jenis_barang', 'nama_barang', 'satuan',
                    'kode_barang', 'nup', 'tahun_perolehan', 'nilai_perolehan',
                    'keterangan', 'asal_usul', 'pengguna', 'kondisi', 'ruangan_id',
                ]);
                // created_by di-set otomatis oleh Inventaris::booted() saat create()
                $newData['status']  = 'draft';
                $newData['barcode'] = $this->generateUniqueBarcode();

                $newInventaris = Inventaris::create($newData);

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

        $namaDisplay = $source->nama_barang ?? $source->kode_barang ?? 'Aset';
        ActivityLogger::log('Inventaris', 'tambah',
            "Menduplikasi aset '{$namaDisplay}' sebanyak {$created} salinan",
            $source->id, $namaDisplay
        );
        \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
        \Illuminate\Support\Facades\Cache::forget('dashboard_charts');

        $msg = $created === 1
            ? "Data Aset {$namaDisplay} berhasil diduplikasi."
            : "{$created} salinan {$namaDisplay} berhasil dibuat.";

        // Kalau AJAX (dari index), return JSON
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => $msg, 'created' => $created]);
        }

        return redirect()->route('manajemen-aset.index')->with('success', $msg);
    }

    public function bulkDestroy(Request $request)    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }
        $totalRequested = count($ids);
        $ids = array_slice($ids, 0, 100);

        $deleted = 0;
        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $inventaris = Inventaris::find($id);
                if (!$inventaris) continue;
                $this->authorize('delete', $inventaris);

                // Cascade: values, export_logs, fotos
                InventarisValue::where('inventaris_id', $id)->delete();
                \App\Models\ExportLog::where('inventaris_id', $id)->update(['inventaris_id' => null]);
                foreach ($inventaris->fotos as $foto) {
                    Storage::disk('public')->delete($foto->path);
                }
                InventarisFoto::where('inventaris_id', $id)->delete();

                // Delete barcode PNG if exists
                $barcodePath = public_path('barcodes/barcode_' . $id . '.png');
                if (file_exists($barcodePath)) {
                    @unlink($barcodePath);
                }

                // Log ke inventaris_deleted_log untuk tracking mutasi berkurang
                \App\Models\InventarisDeletedLog::create([
                    'kode_barang'     => $inventaris->kode_barang,
                    'nup'             => $inventaris->nup,
                    'nama_barang'     => $inventaris->nama_barang,
                    'satuan'          => $inventaris->satuan,
                    'nilai_perolehan' => $inventaris->nilai_perolehan,
                    'tahun_perolehan' => $inventaris->tahun_perolehan,
                    'deleted_at'      => now(),
                    'deleted_by'      => auth()->id(),
                ]);

                $inventaris->delete();
                $deleted++;
            }
            DB::commit();
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
            $msg = $deleted . ' inventaris berhasil dihapus.';
            if ($totalRequested > 100) {
                $msg .= ' Sisa ' . ($totalRequested - 100) . ' data, pilih ulang untuk melanjutkan.';
            }
            return redirect()->route('manajemen-aset.index')->with('success', $msg);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Spesifik dulu: akses ditolak → rollback lalu lempar ke Laravel (tampil 403)
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            // General error: rollback, log, redirect dengan pesan error
            DB::rollBack();
            Log::error('ManajemenAsetController::bulkDestroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

