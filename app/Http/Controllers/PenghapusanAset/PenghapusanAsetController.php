<?php

namespace App\Http\Controllers\PenghapusanAset;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Models\Inventaris;
use App\Models\InventarisDeletedLog;
use App\Models\InventarisFoto;
use App\Models\InventarisKibValue;
use App\Models\InventarisKirValue;
use App\Models\InventarisLaporanAsetValue;
use App\Models\InventarisValue;
use App\Models\JenisInventaris;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PenghapusanAsetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'aktif');
        if (!in_array($activeTab, ['aktif', 'riwayat'])) {
            $activeTab = 'aktif';
        }

        // Total badge counters
        $totalActiveCount  = Inventaris::count();
        $totalDeletedCount = InventarisDeletedLog::count();

        // =========================================================================
        // 1. Data untuk Tab Aset Aktif (Siap Dihapus)
        // Berdiri sendiri: hanya menerima filter & per_page jika tab aktif adalah 'aktif'
        // =========================================================================
        $activePerPage = 10;
        $activeSort    = 'terbaru';
        $activeQuery   = Inventaris::with(['jenis', 'ruangan', 'kibValues', 'kirValues', 'values.field']);

        if ($activeTab === 'aktif') {
            $activePerPage = (int) $request->input('per_page', $request->input('limit', 10));
            if (!in_array($activePerPage, [10, 20, 50, 100])) {
                $activePerPage = 10;
            }
            $activeSort = $request->input('sort', 'terbaru');

            if ($request->filled('search')) {
                $search = trim($request->search);
                $activeQuery->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('kode_barang', 'like', "%{$search}%")
                      ->orWhere('nup', 'like', "%{$search}%");
                });
            }
            if ($request->has('jenis') && is_array($request->jenis) && count($request->jenis) > 0) {
                $activeQuery->whereIn('jenis_inventaris_id', $request->jenis);
            } elseif ($request->filled('jenis') && $request->jenis !== 'all') {
                $activeQuery->where('jenis_inventaris_id', $request->jenis);
            }
            if ($request->filled('tahun') && $request->tahun !== 'all') {
                $activeQuery->where('tahun_perolehan', $request->tahun);
            }
            if ($request->filled('ruangan') && $request->ruangan !== 'all') {
                if ($request->ruangan === 'none') {
                    $activeQuery->whereNull('ruangan_id');
                } else {
                    $activeQuery->where('ruangan_id', $request->ruangan);
                }
            }
            if ($request->has('kondisi')) {
                $kondisi = (array) $request->kondisi;
                $validKondisi = array_values(array_filter($kondisi, fn($k) => in_array(strtolower($k), ['baik', 'rusak_ringan', 'rusak_berat'])));
                if (!empty($validKondisi)) {
                    $activeQuery->where(function($q) use ($validKondisi) {
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
        }

        match ($activeSort) {
            'nama_desc' => $activeQuery->orderBy('nama_barang', 'desc'),
            'terbaru'   => $activeQuery->orderBy('id', 'desc'),
            'terlama'   => $activeQuery->orderBy('id', 'asc'),
            default     => $activeQuery->orderBy('nama_barang', 'asc'),
        };

        // Paginate Tab Aktif:
        // Jika activeTab === 'aktif', gunakan parameter 'page'. Jika bukan, jangan gunakan 'page' milik tab riwayat
        $activePageName = ($activeTab === 'aktif') ? 'page' : 'page_aktif_inactive';
        $activeAssets = $activeQuery->paginate($activePerPage, ['*'], $activePageName, ($activeTab === 'aktif' ? null : 1))->withQueryString();

        // =========================================================================
        // 2. Data untuk Tab Riwayat Aset Dihapus (Mutasi Berkurang)
        // Berdiri sendiri: hanya menerima filter & per_page jika tab aktif adalah 'riwayat'
        // =========================================================================
        $deletedPerPage = 10;
        $deletedSort    = 'terbaru';
        $deletedQuery   = InventarisDeletedLog::with('user:id,nama');

        if ($activeTab === 'riwayat') {
            $deletedPerPage = (int) $request->input('per_page', $request->input('limit', 10));
            if (!in_array($deletedPerPage, [10, 20, 50, 100])) {
                $deletedPerPage = 10;
            }
            $deletedSort = $request->input('sort', 'terbaru');

            if ($request->filled('search')) {
                $search = trim($request->search);
                $deletedQuery->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('kode_barang', 'like', "%{$search}%")
                      ->orWhere('nup', 'like', "%{$search}%")
                      ->orWhere('nomor_sk', 'like', "%{$search}%")
                      ->orWhere('alasan_penghapusan', 'like', "%{$search}%")
                      ->orWhere('ruangan_nama', 'like', "%{$search}%");
                });
            }
            if ($request->has('jenis_nama') && is_array($request->jenis_nama) && count($request->jenis_nama) > 0) {
                $deletedQuery->whereIn('jenis_inventaris_nama', $request->jenis_nama);
            } elseif ($request->has('jenis') && is_array($request->jenis) && count($request->jenis) > 0) {
                $deletedQuery->whereIn('jenis_inventaris_nama', $request->jenis);
            } elseif ($request->filled('jenis_nama') && $request->jenis_nama !== 'all') {
                $deletedQuery->where('jenis_inventaris_nama', $request->jenis_nama);
            } elseif ($request->filled('jenis') && $request->jenis !== 'all') {
                $deletedQuery->where('jenis_inventaris_nama', $request->jenis);
            }
            if ($request->filled('tahun_hapus') && $request->tahun_hapus !== 'all') {
                $deletedQuery->whereYear('deleted_at', $request->tahun_hapus);
            } elseif ($request->filled('tahun') && $request->tahun !== 'all') {
                $deletedQuery->whereYear('deleted_at', $request->tahun);
            }
            if ($request->filled('tahun_perolehan') && $request->tahun_perolehan !== 'all') {
                $deletedQuery->where('tahun_perolehan', $request->tahun_perolehan);
            }
            if ($request->filled('ruangan_nama') && $request->ruangan_nama !== 'all') {
                if ($request->ruangan_nama === 'none') {
                    $deletedQuery->where(function($q) {
                        $q->whereNull('ruangan_nama')->orWhere('ruangan_nama', '-')->orWhere('ruangan_nama', '');
                    });
                } else {
                    $deletedQuery->where('ruangan_nama', $request->ruangan_nama);
                }
            }
        }

        match ($deletedSort) {
            'nama_desc' => $deletedQuery->orderBy('nama_barang', 'desc'),
            'terbaru'   => $deletedQuery->orderBy('deleted_at', 'desc'),
            'terlama'   => $deletedQuery->orderBy('deleted_at', 'asc'),
            default     => $deletedQuery->orderBy('nama_barang', 'asc'),
        };

        // Paginate Tab Riwayat:
        // Jika activeTab === 'riwayat', gunakan parameter 'page'. Jika bukan, jangan gunakan 'page' milik tab aktif
        $deletedPageName = ($activeTab === 'riwayat') ? 'page' : 'page_riwayat_inactive';
        $deletedLogs = $deletedQuery->paginate($deletedPerPage, ['*'], $deletedPageName, ($activeTab === 'riwayat' ? null : 1))->withQueryString();

        // Summary Stats (4 Standard KPI Cards)
        $statsQuery = InventarisDeletedLog::query();
        $totalDihapus    = $statsQuery->count();
        $totalNilai      = (float) $statsQuery->sum('nilai_perolehan');
        $dihapusTahunIni = InventarisDeletedLog::whereYear('deleted_at', date('Y'))->count();

        // Monthly Trend
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $dihapusThisMonth = InventarisDeletedLog::where('deleted_at', '>=', $thisMonthStart)->count();
        $dihapusLastMonth = InventarisDeletedLog::where('deleted_at', '>=', $lastMonthStart)->where('deleted_at', '<', $thisMonthStart)->count();
        $diffDihapus = $dihapusThisMonth - $dihapusLastMonth;
        $percentDihapus = $dihapusLastMonth > 0 ? round(($diffDihapus / $dihapusLastMonth) * 100, 1) : ($dihapusThisMonth > 0 ? 100 : 0);

        $nilaiThisMonth = (float) InventarisDeletedLog::where('deleted_at', '>=', $thisMonthStart)->sum('nilai_perolehan');
        $nilaiLastMonth = (float) InventarisDeletedLog::where('deleted_at', '>=', $lastMonthStart)->where('deleted_at', '<', $thisMonthStart)->sum('nilai_perolehan');
        $diffNilai = $nilaiThisMonth - $nilaiLastMonth;
        $percentNilai = $nilaiLastMonth > 0 ? round(($diffNilai / $nilaiLastMonth) * 100, 1) : ($nilaiThisMonth > 0 ? 100 : 0);

        $trends = [
            'totalDihapus'    => ['diff' => $diffDihapus, 'percent' => $percentDihapus],
            'totalNilai'      => ['diff' => $diffNilai, 'percent' => $percentNilai],
        ];

        // Data untuk Filter Dropdowns (Kecualikan 'Buku Inventaris Aset Desa' karena merupakan jenis virtual)
        $jenisList = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('nama')
            ->get(['id', 'nama']);
        $ruanganList = Ruangan::orderBy('nama')->get(['id', 'nama', 'kode']);
        $tahunList = Inventaris::whereNotNull('tahun_perolehan')->selectRaw('DISTINCT tahun_perolehan')->orderBy('tahun_perolehan', 'desc')->pluck('tahun_perolehan');

        $years = InventarisDeletedLog::whereNotNull('deleted_at')
            ->selectRaw('DISTINCT YEAR(deleted_at) as tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $alasanList = [
            'Rusak Berat / Tidak Dapat Dipakai' => 'Rusak Berat / Tidak Dapat Dipakai',
            'Pemusnahan'                        => 'Pemusnahan',
            'Pemindahtanganan (Hibah / Penjualan)' => 'Pemindahtanganan (Hibah / Penjualan)',
            'Hilang / Keadaan Kahar'            => 'Hilang / Keadaan Kahar',
            'Lainnya'                           => 'Lainnya',
        ];

        // Cek jika ada request dari Manajemen Aset dengan ?inventaris_id=XX atau ?inventaris_ids=XX,YY
        $preselectedAset = null;
        $preselectedAsets = collect();
        if ($request->filled('inventaris_ids')) {
            $rawIds = is_array($request->inventaris_ids) ? $request->inventaris_ids : explode(',', $request->inventaris_ids);
            $rawIds = array_filter(array_map('intval', $rawIds));
            if (!empty($rawIds)) {
                $preselectedAsets = Inventaris::with(['jenis', 'ruangan'])->whereIn('id', $rawIds)->get();
                $activeTab = 'aktif';
            }
        } elseif ($request->filled('inventaris_id')) {
            $preselectedAset = Inventaris::with(['jenis', 'ruangan'])->find($request->inventaris_id);
            $activeTab = 'aktif';
        }

        // Kategori sering dihapus:
        // Sinkronisasi record lawas yang jenis_inventaris_nama nya null/kosong (hanya dieksekusi jika ada record yang belum tersinkron)
        $hasUnresolvedLogs = InventarisDeletedLog::whereNull('jenis_inventaris_nama')
            ->orWhere('jenis_inventaris_nama', '')
            ->exists();

        if ($hasUnresolvedLogs) {
            InventarisDeletedLog::whereNull('jenis_inventaris_nama')
                ->orWhere('jenis_inventaris_nama', '')
                ->get()
                ->each(function ($log) {
                    $resolvedName = $log->jenis_nama ?: 'Aset Tetap Lainnya';
                    $log->update(['jenis_inventaris_nama' => $resolvedName]);
                });
        }

        $topKategoriRecord = InventarisDeletedLog::whereNotNull('jenis_inventaris_nama')
            ->where('jenis_inventaris_nama', '!=', '')
            ->select('jenis_inventaris_nama', DB::raw('count(*) as total'))
            ->groupBy('jenis_inventaris_nama')
            ->orderBy('total', 'desc')
            ->first();

        $topKategoriNama = $topKategoriRecord ? $topKategoriRecord->jenis_inventaris_nama : '-';
        $topKategoriTotal = $topKategoriRecord ? $topKategoriRecord->total : 0;

        // Jika request via AJAX atau expects JSON, kirim response JSON agar tidak perlu refresh halaman
        if ($request->ajax() || $request->wantsJson()) {
            if ($activeTab === 'aktif') {
                $items = collect($activeAssets->items())->map(function ($item) {
                    $jenis = $item->jenis;
                    $colors = $jenis?->color_gradient ?? ['#14b8a6', '#0d9488'];
                    return [
                        'id' => $item->id,
                        'nama_barang' => $item->nama_barang,
                        'kode_barang' => $item->kode_barang ?? '-',
                        'nup' => $item->nup ?: '1',
                        'merk_type' => $item->merk_type,
                        'tahun_perolehan' => $item->tahun_perolehan ?? '-',
                        'nilai_perolehan' => $item->nilai_perolehan,
                        'ruangan_nama' => $item->ruangan ? $item->ruangan->nama : '-',
                        'jenis_nama' => $jenis?->nama ?? 'Aset Tetap Lainnya',
                        'jenis_icon' => $jenis?->icon ?? 'layers',
                        'jenis_color0' => $colors[0] ?? '#14b8a6',
                        'jenis_color1' => $colors[1] ?? '#0d9488',
                        'kondisi' => $item->kondisi ?? 'baik',
                    ];
                });

                return response()->json([
                    'tab' => 'aktif',
                    'items' => $items,
                    'total' => $activeAssets->total(),
                    'from' => $activeAssets->firstItem() ?? 0,
                    'to' => $activeAssets->lastItem() ?? 0,
                    'pagination' => [
                        'firstItem' => $activeAssets->firstItem() ?? 0,
                        'lastItem' => $activeAssets->lastItem() ?? 0,
                        'currentPage' => $activeAssets->currentPage(),
                        'lastPage' => $activeAssets->lastPage(),
                        'perPage' => $activeAssets->perPage(),
                        'total' => $activeAssets->total(),
                    ],
                    'totalActiveCount' => $totalActiveCount,
                    'canDelete' => Auth::user()->canDeleteAset(),
                ]);
            } else {
                $items = collect($deletedLogs->items())->map(function ($log) {
                    $colors = $log->jenis_colors ?? ['#14b8a6', '#0d9488'];
                    return [
                        'id' => $log->id,
                        'nama_barang' => $log->nama_barang,
                        'kode_barang' => $log->kode_barang ?? '-',
                        'nup' => $log->nup ?: '1',
                        'merk_type' => $log->merk_type ?? '-',
                        'tahun_perolehan' => $log->tahun_perolehan ?? '-',
                        'nilai_perolehan' => $log->nilai_perolehan,
                        'ruangan_nama' => (!empty($log->ruangan_nama) && $log->ruangan_nama !== '-') ? $log->ruangan_nama : '-',
                        'jenis_nama' => $log->jenis_nama,
                        'jenis_icon' => $log->jenis_icon ?? 'layers',
                        'jenis_color0' => $colors[0] ?? '#14b8a6',
                        'jenis_color1' => $colors[1] ?? '#0d9488',
                        'tanggal_hapus' => $log->tanggal_hapus ? \Carbon\Carbon::parse($log->tanggal_hapus)->translatedFormat('d M Y') : ($log->deleted_at ? $log->deleted_at->translatedFormat('d M Y') : '-'),
                    ];
                });

                return response()->json([
                    'tab' => 'riwayat',
                    'items' => $items,
                    'total' => $deletedLogs->total(),
                    'from' => $deletedLogs->firstItem() ?? 0,
                    'to' => $deletedLogs->lastItem() ?? 0,
                    'pagination' => [
                        'firstItem' => $deletedLogs->firstItem() ?? 0,
                        'lastItem' => $deletedLogs->lastItem() ?? 0,
                        'currentPage' => $deletedLogs->currentPage(),
                        'lastPage' => $deletedLogs->lastPage(),
                        'perPage' => $deletedLogs->perPage(),
                        'total' => $deletedLogs->total(),
                    ],
                    'stats' => [
                        'totalDihapus' => $totalDihapus,
                        'totalNilai' => $totalNilai,
                        'dihapusTahunIni' => $dihapusTahunIni,
                        'topKategoriNama' => $topKategoriNama,
                        'topKategoriTotal' => $topKategoriTotal,
                        'trends' => $trends,
                    ],
                    'totalDeletedCount' => $totalDeletedCount,
                ]);
            }
        }

        // Ambil daftar dokumen unik yang pernah diunggah untuk opsi pemilihan dokumen eksisting
        $existingDocuments = InventarisDeletedLog::query()
            ->whereNotNull('dokumen_pdf_path')
            ->where('dokumen_pdf_path', '!=', '')
            ->select('dokumen_pdf_path', 'dokumen_pdf_name')
            ->distinct()
            ->get()
            ->filter(fn($doc) => Storage::disk('public')->exists($doc->dokumen_pdf_path))
            ->unique('dokumen_pdf_path')
            ->map(function($doc) {
                $ext = strtolower(pathinfo($doc->dokumen_pdf_path, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true);
                $formattedSize = '-';
                try {
                    $bytes = Storage::disk('public')->size($doc->dokumen_pdf_path);
                    $formattedSize = $bytes >= 1048576 
                        ? number_format($bytes / 1048576, 2) . ' MB'
                        : number_format($bytes / 1024, 1) . ' KB';
                } catch (\Throwable $e) {}
                return [
                    'path'     => $doc->dokumen_pdf_path,
                    'filename' => $doc->dokumen_pdf_name ?: basename($doc->dokumen_pdf_path),
                    'url'      => Storage::disk('public')->url($doc->dokumen_pdf_path),
                    'is_image' => $isImage,
                    'ext'      => $ext,
                    'size'     => $formattedSize,
                ];
            })
            ->values();

        return view('penghapusan-aset.index', compact(
            'activeTab',
            'activeAssets',
            'deletedLogs',
            'totalActiveCount',
            'totalDeletedCount',
            'totalDihapus',
            'totalNilai',
            'dihapusTahunIni',
            'topKategoriNama',
            'topKategoriTotal',
            'trends',
            'jenisList',
            'ruanganList',
            'tahunList',
            'years',
            'alasanList',
            'preselectedAset',
            'preselectedAsets',
            'existingDocuments'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canDeleteAset()) {
            abort(403, 'Hanya Kaur Umum, Sekretaris Desa, Perbekel, dan Super Admin yang berhak menghapus aset secara resmi.');
        }

        $ids = [];
        if ($request->filled('inventaris_ids')) {
            $ids = is_array($request->inventaris_ids) ? $request->inventaris_ids : explode(',', $request->inventaris_ids);
        } elseif ($request->filled('inventaris_id')) {
            $ids = [$request->inventaris_id];
        }

        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih setidaknya 1 aset untuk dihapus.')->withInput();
        }

        $metodeDokumen = $request->input('metode_dokumen', 'upload');

        $validationRules = [
            'tanggal_hapus'      => ['required', 'date', 'before_or_equal:today'],
            'alasan_penghapusan' => ['nullable', 'string', 'max:150'],
            'nomor_sk'           => ['nullable', 'string', 'max:150'],
            'keterangan'         => ['nullable', 'string', 'max:1000'],
        ];

        if ($metodeDokumen === 'existing') {
            $validationRules['existing_dokumen_path'] = ['required', 'string'];
        } else {
            $validationRules['dokumen_pdf'] = ['required', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:10240']; // max 10MB
        }

        $request->validate($validationRules, [
            'tanggal_hapus.required'         => 'Tanggal penghapusan wajib diisi.',
            'tanggal_hapus.before_or_equal'  => 'Tanggal penghapusan tidak boleh di masa depan (maksimal hari ini).',
            'dokumen_pdf.required'           => 'Dokumen Berita Acara / SK atau Foto bukti penghapusan wajib diunggah.',
            'dokumen_pdf.mimes'              => 'Format file harus berupa PDF, PNG, JPG, JPEG, atau WEBP.',
            'dokumen_pdf.max'                => 'Ukuran file maksimal 10 MB.',
            'existing_dokumen_path.required' => 'Pilih dokumen yang sudah ada terlebih dahulu.',
        ]);

        $assets = Inventaris::with(['jenis', 'fotos', 'kibValues', 'kirValues', 'values.field', 'laporanAsetValue'])->whereIn('id', $ids)->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'Aset yang dipilih tidak ditemukan atau sudah dihapus.')->withInput();
        }

        // Tentukan path dan nama dokumen bukti penghapusan
        if ($metodeDokumen === 'existing') {
            $existingPath = $request->input('existing_dokumen_path');
            $prevLog = InventarisDeletedLog::where('dokumen_pdf_path', $existingPath)->whereNotNull('dokumen_pdf_name')->first();
            if (!$prevLog || !Storage::disk('public')->exists($existingPath)) {
                return redirect()->back()->with('error', 'File dokumen yang dipilih tidak valid atau tidak ditemukan di storage.')->withInput();
            }
            $pdfPath = $existingPath;
            $originalPdfName = $prevLog->dokumen_pdf_name ?: basename($existingPath);
        } else {
            $pdfFile = $request->file('dokumen_pdf');
            $originalPdfName = $pdfFile->getClientOriginalName();
            $pdfPath = $pdfFile->store('dokumen-penghapusan', 'public');
        }

        $nomorSk = $request->nomor_sk ?: '-';
        $alasan = $request->alasan_penghapusan ?: 'Penghapusan Resmi';

        try {
            DB::beginTransaction();

            $deletedCount = 0;
            $assetNames = [];

            foreach ($assets as $inventaris) {
                $id = $inventaris->id;
                $kodeBarang = $inventaris->kode_barang;
                $namaBarang = $inventaris->nama_barang;
                $jenisNama  = $inventaris->jenis ? $inventaris->jenis->nama : ($inventaris->jenis_barang ?? 'Aset Tetap Lainnya');
                $assetNames[] = $namaBarang;

                // Snapshot lengkap data aset untuk fitur restore
                $snapshot = [
                    'inventaris'      => $inventaris->makeHidden(['jenis', 'ruangan', 'fotos', 'kibValues', 'kirValues', 'values', 'laporanAsetValue'])->toArray(),
                    'kib_values'      => $inventaris->kibValues ? $inventaris->kibValues->toArray() : [],
                    'kir_values'      => $inventaris->kirValues ? $inventaris->kirValues->toArray() : [],
                    'values'          => $inventaris->values ? $inventaris->values->toArray() : [],
                    'bulan_perolehan' => $inventaris->laporanAsetValue?->bulan_perolehan,
                ];

                // 1. Simpan ke inventaris_deleted_log
                $logData = [
                    'kode_barang'           => $inventaris->kode_barang,
                    'nup'                   => $inventaris->nup,
                    'nama_barang'           => $inventaris->nama_barang,
                    'jenis_inventaris_nama' => $jenisNama,
                    'merk_type'             => $inventaris->merk_type,
                    'satuan'                => $inventaris->satuan,
                    'nilai_perolehan'       => $inventaris->nilai_perolehan,
                    'tahun_perolehan'       => $inventaris->tahun_perolehan,
                    'ruangan_nama'          => $inventaris->ruangan ? $inventaris->ruangan->nama : null,
                    'nomor_sk'              => $nomorSk,
                    'alasan_penghapusan'    => $alasan,
                    'keterangan'            => $request->keterangan,
                    'dokumen_pdf_path'      => $pdfPath,
                    'dokumen_pdf_name'      => $originalPdfName,
                    'tanggal_hapus'         => $request->tanggal_hapus,
                    'deleted_at'            => $request->tanggal_hapus ? $request->tanggal_hapus . ' ' . date('H:i:s') : now(),
                    'deleted_by'            => Auth::id(),
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('inventaris_deleted_log', 'asset_data_snapshot')) {
                    $logData['asset_data_snapshot'] = $snapshot;
                }

                $deletedLog = InventarisDeletedLog::create($logData);

                // 2. Cascade delete relasi aset
                InventarisValue::where('inventaris_id', $id)->delete();
                InventarisKibValue::where('inventaris_id', $id)->delete();
                InventarisKirValue::where('inventaris_id', $id)->delete();
                InventarisLaporanAsetValue::where('inventaris_id', $id)->delete();
                \App\Models\ExportLog::where('inventaris_id', $id)->update(['inventaris_id' => null]);

                // 3. Hapus foto dari storage
                foreach ($inventaris->fotos as $foto) {
                    Storage::disk('public')->delete($foto->path);
                }
                InventarisFoto::where('inventaris_id', $id)->delete();

                // 4. Hapus barcode PNG jika ada
                $barcodePath = public_path('barcodes/barcode_' . $id . '.png');
                if (file_exists($barcodePath)) {
                    @unlink($barcodePath);
                }

                // 5. Hapus record aset dari inventaris
                $inventaris->delete();
                $deletedCount++;
            }

            DB::commit();

            ActivityLogger::log(
                'Penghapusan Aset',
                'hapus',
                "Penghapusan Resmi {$deletedCount} Aset (SK: {$request->nomor_sk})",
                null,
                implode(', ', array_slice($assetNames, 0, 5)) . ($deletedCount > 5 ? ' dkk.' : '')
            );

            Cache::forget('dashboard_kpi');
            Cache::forget('dashboard_charts');

            $msg = $deletedCount === 1 
                ? "Aset \"{$assetNames[0]}\" berhasil dihapus secara resmi dan dicatat ke Mutasi Berkurang."
                : "Sebanyak {$deletedCount} aset berhasil dihapus secara resmi dan dicatat ke Mutasi Berkurang dengan 1 Dokumen SK/Berita Acara.";

            return redirect()->route('penghapusan-aset.index', ['tab' => 'aktif'])->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            // Rollback uploaded PDF if failed (hanya hapus berkas jika diunggah baru pada request ini)
            if ($metodeDokumen !== 'existing' && !empty($pdfPath) && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }

            Log::error('Penghapusan Aset Resmi Gagal', [
                'inventaris_ids' => $ids,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Gagal memproses penghapusan aset: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $log = InventarisDeletedLog::with('user:id,nama,email')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                    => $log->id,
                'kode_barang'           => $log->kode_barang,
                'nup'                   => $log->nup,
                'nama_barang'           => $log->nama_barang,
                'jenis_inventaris_nama' => $log->jenis_nama ?: ($log->jenis_inventaris_nama ?: 'Aset Tetap Lainnya'),
                'merk_type'             => $log->merk_type ?? '-',
                'satuan'                => $log->satuan ?? '-',
                'nilai_perolehan'       => $log->nilai_perolehan ? 'Rp ' . number_format($log->nilai_perolehan, fmod($log->nilai_perolehan, 1) != 0 ? 2 : 0, ',', '.') : '-',
                'tahun_perolehan'       => $log->tahun_perolehan ?? '-',
                'ruangan_nama'          => $log->ruangan_nama ?? '-',
                'nomor_sk'              => $log->nomor_sk ?? '-',
                'alasan_penghapusan'    => $log->alasan_penghapusan ?? '-',
                'keterangan'            => $log->keterangan ?? '-',
                'tanggal_hapus'         => $log->tanggal_hapus ? \Carbon\Carbon::parse($log->tanggal_hapus)->translatedFormat('d F Y') : ($log->deleted_at ? $log->deleted_at->translatedFormat('d F Y') : '-'),
                'deleted_by'            => $log->user->nama ?? 'Administrator',
                'dokumen_pdf_name'      => $log->dokumen_pdf_name,
                'dokumen_pdf_url'       => $log->dokumen_pdf_path ? Storage::disk('public')->url($log->dokumen_pdf_path) : null,
                'download_url'          => route('penghapusan-aset.download-pdf', $log->id),
            ]
        ]);
    }

    public function downloadPdf($id)
    {
        if (!Auth::user()->canDeleteAset()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh dokumen bukti penghapusan aset.');
        }

        $log = InventarisDeletedLog::findOrFail($id);

        if (!$log->dokumen_pdf_path || !Storage::disk('public')->exists($log->dokumen_pdf_path)) {
            abort(404, 'Dokumen PDF tidak ditemukan.');
        }

        $filename = $log->dokumen_pdf_name ?: ('SK_Penghapusan_' . $log->kode_barang . '.pdf');

        return Storage::disk('public')->download($log->dokumen_pdf_path, $filename);
    }

    public function restore(Request $request)
    {
        if (!Auth::user()->canDeleteAset()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk memulihkan aset.'], 403);
            }
            abort(403, 'Hanya Kaur Umum, Sekretaris Desa, Perbekel, dan Super Admin yang berhak memulihkan aset.');
        }

        $ids = [];
        if ($request->filled('log_ids')) {
            $ids = is_array($request->log_ids) ? $request->log_ids : explode(',', $request->log_ids);
        } elseif ($request->filled('log_id')) {
            $ids = [$request->log_id];
        } elseif ($request->filled('id')) {
            $ids = [$request->id];
        }

        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Pilih setidaknya 1 aset dari riwayat untuk dipulihkan.'], 422);
            }
            return redirect()->back()->with('error', 'Pilih setidaknya 1 aset dari riwayat untuk dipulihkan.');
        }

        $logs = InventarisDeletedLog::whereIn('id', $ids)->get();

        if ($logs->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data riwayat aset tidak ditemukan.'], 404);
            }
            return redirect()->back()->with('error', 'Data riwayat aset tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            $restoredCount = 0;
            $assetNames = [];

            foreach ($logs as $log) {
                $snapshot = $log->asset_data_snapshot;
                $assetNames[] = $log->nama_barang;

                // Jika memiliki data snapshot lengkap
                if (!empty($snapshot) && isset($snapshot['inventaris'])) {
                    $invData = $snapshot['inventaris'];
                    unset($invData['id'], $invData['created_at'], $invData['updated_at']);
                    
                    $invData['updated_by'] = Auth::id();

                    // Cek jika kode_barang dan NUP sudah terpakai oleh aset aktif lain
                    if (!empty($invData['kode_barang'])) {
                        $nupCollision = Inventaris::where('kode_barang', $invData['kode_barang'])
                            ->where('nup', $invData['nup'] ?? null)
                            ->exists();
                        if ($nupCollision) {
                            $maxExistingNup = Inventaris::where('kode_barang', $invData['kode_barang'])
                                ->pluck('nup')
                                ->map(fn($v) => is_numeric($v) ? (int)$v : 0)
                                ->max() ?: 0;
                            $invData['nup'] = (string)($maxExistingNup + 1);
                        }
                    }
                    
                    $statusToRestore = !empty($invData['status']) ? $invData['status'] : 'disetujui';
                    if (empty($invData['kondisi'])) {
                        $invData['kondisi'] = 'baik';
                    }
                    $restoredAsset = Inventaris::create($invData);
                    $restoredAsset->status = $statusToRestore;
                    if (empty($restoredAsset->barcode)) {
                        $restoredAsset->barcode = Inventaris::generateBarcode();
                    }
                    $restoredAsset->save();

                    // Restore KIB values
                    if (!empty($snapshot['kib_values'])) {
                        foreach ($snapshot['kib_values'] as $kib) {
                            if (isset($kib['kib_field_key'])) {
                                unset($kib['id'], $kib['created_at'], $kib['updated_at']);
                                $kib['inventaris_id'] = $restoredAsset->id;
                                InventarisKibValue::create($kib);
                            } else {
                                foreach ($kib as $kKey => $kVal) {
                                    if (!in_array($kKey, ['id', 'inventaris_id', 'created_at', 'updated_at'])) {
                                        InventarisKibValue::create([
                                            'inventaris_id' => $restoredAsset->id,
                                            'kib_field_key' => $kKey,
                                            'value'         => $kVal,
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    // Restore KIR values
                    if (!empty($snapshot['kir_values'])) {
                        foreach ($snapshot['kir_values'] as $kir) {
                            if (isset($kir['kir_field_key'])) {
                                unset($kir['id'], $kir['created_at'], $kir['updated_at']);
                                $kir['inventaris_id'] = $restoredAsset->id;
                                InventarisKirValue::create($kir);
                            } else {
                                foreach ($kir as $kKey => $kVal) {
                                    if (!in_array($kKey, ['id', 'inventaris_id', 'created_at', 'updated_at'])) {
                                        InventarisKirValue::create([
                                            'inventaris_id' => $restoredAsset->id,
                                            'kir_field_key' => $kKey,
                                            'value'         => $kVal,
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    // Restore dynamic values (values kustom)
                    if (!empty($snapshot['values'])) {
                        foreach ($snapshot['values'] as $val) {
                            $fieldId = $val['jenis_inventaris_data_id'] ?? $val['field_id'] ?? null;
                            if ($fieldId && \App\Models\JenisInventarisData::where('id', $fieldId)->exists()) {
                                InventarisValue::create([
                                    'inventaris_id'            => $restoredAsset->id,
                                    'jenis_inventaris_data_id' => $fieldId,
                                    'value'                    => $val['value'] ?? null,
                                ]);
                            }
                        }
                    }

                    // Restore nilai laporan aset (bulan perolehan untuk pembagian semester)
                    if (isset($snapshot['bulan_perolehan']) && $snapshot['bulan_perolehan'] !== null && $snapshot['bulan_perolehan'] !== '') {
                        InventarisLaporanAsetValue::updateOrCreate(
                            ['inventaris_id' => $restoredAsset->id],
                            ['bulan_perolehan' => (int) $snapshot['bulan_perolehan']]
                        );
                    }
                } else {
                    // Fallback untuk log lama yang belum memiliki snapshot
                    $jenisModel = $log->getJenisModel();
                    $ruanganModel = !empty($log->ruangan_nama) ? Ruangan::where('nama', $log->ruangan_nama)->first() : null;

                    $targetKode = $log->kode_barang;
                    $targetNup = $log->nup;
                    if (!empty($targetKode)) {
                        $nupCollision = Inventaris::where('kode_barang', $targetKode)
                            ->where('nup', $targetNup)
                            ->exists();
                        if ($nupCollision) {
                            $maxExistingNup = Inventaris::where('kode_barang', $targetKode)
                                ->pluck('nup')
                                ->map(fn($v) => is_numeric($v) ? (int)$v : 0)
                                ->max() ?: 0;
                            $targetNup = (string)($maxExistingNup + 1);
                        }
                    }

                    $restoredAsset = Inventaris::create([
                        'jenis_inventaris_id' => $jenisModel ? $jenisModel->id : (JenisInventaris::first()?->id ?? 1),
                        'nama_barang'         => $log->nama_barang,
                        'satuan'              => $log->satuan ?? 'Unit',
                        'kode_barang'         => $targetKode,
                        'nup'                 => $targetNup,
                        'tahun_perolehan'     => $log->tahun_perolehan,
                        'nilai_perolehan'     => $log->nilai_perolehan,
                        'keterangan'          => $log->keterangan,
                        'ruangan_id'          => $ruanganModel?->id,
                        'kondisi'             => 'baik',
                        'created_by'          => Auth::id(),
                        'updated_by'          => Auth::id(),
                    ]);
                    $restoredAsset->status = 'disetujui';
                    if (empty($restoredAsset->barcode)) {
                        $restoredAsset->barcode = Inventaris::generateBarcode();
                    }
                    $restoredAsset->save();

                    // Simpan merk_type ke KIB jika ada
                    if (!empty($log->merk_type) && $log->merk_type !== '-') {
                        InventarisKibValue::create([
                            'inventaris_id' => $restoredAsset->id,
                            'kib_field_key' => 'merk_type',
                            'value'         => $log->merk_type,
                        ]);
                    }
                }

                // Hapus log dari riwayat mutasi berkurang
                $log->delete();
                $restoredCount++;
            }

            DB::commit();

            ActivityLogger::log(
                'Penghapusan Aset',
                'restore',
                "Memulihkan {$restoredCount} Aset dari Riwayat Penghapusan",
                null,
                implode(', ', array_slice($assetNames, 0, 5)) . ($restoredCount > 5 ? ' dkk.' : '')
            );

            Cache::forget('dashboard_kpi');
            Cache::forget('dashboard_charts');

            $msg = $restoredCount === 1
                ? "Aset \"{$assetNames[0]}\" berhasil dipulihkan kembali ke Daftar Aset Aktif."
                : "Sebanyak {$restoredCount} aset berhasil dipulihkan kembali ke Daftar Aset Aktif.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'        => true,
                    'message'        => $msg,
                    'restored_count' => $restoredCount,
                ]);
            }

            return redirect()->route('penghapusan-aset.index', ['tab' => 'riwayat'])->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Restore Aset Gagal', [
                'log_ids' => $ids,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memulihkan aset: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memulihkan aset: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor rekapitulasi data penghapusan aset (mutasi berkurang) ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Inventaris::class);
        $format = strtolower($request->query('format', 'excel'));

        $query = InventarisDeletedLog::with('user:id,nama');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nup', 'like', "%{$search}%")
                  ->orWhere('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('alasan_penghapusan', 'like', "%{$search}%")
                  ->orWhere('ruangan_nama', 'like', "%{$search}%");
            });
        }

        if ($request->has('jenis_nama') && is_array($request->jenis_nama) && count($request->jenis_nama) > 0) {
            $query->whereIn('jenis_inventaris_nama', $request->jenis_nama);
        } elseif ($request->has('jenis') && is_array($request->jenis) && count($request->jenis) > 0) {
            $query->whereIn('jenis_inventaris_nama', $request->jenis);
        } elseif ($request->filled('jenis_nama') && $request->jenis_nama !== 'all') {
            $query->where('jenis_inventaris_nama', $request->jenis_nama);
        } elseif ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('jenis_inventaris_nama', $request->jenis);
        }

        if ($request->filled('tahun_hapus') && $request->tahun_hapus !== 'all') {
            $query->whereYear('deleted_at', $request->tahun_hapus);
        } elseif ($request->filled('tahun') && $request->tahun !== 'all' && is_numeric($request->tahun)) {
            $query->whereYear('deleted_at', (int)$request->tahun);
        }

        if ($request->filled('tahun_perolehan') && $request->tahun_perolehan !== 'all') {
            $query->where('tahun_perolehan', $request->tahun_perolehan);
        }

        if ($request->filled('ruangan_nama') && $request->ruangan_nama !== 'all') {
            if ($request->ruangan_nama === 'none') {
                $query->where(function($q) {
                    $q->whereNull('ruangan_nama')->orWhere('ruangan_nama', '-')->orWhere('ruangan_nama', '');
                });
            } else {
                $query->where('ruangan_nama', $request->ruangan_nama);
            }
        }

        $logs = $query->orderByDesc('deleted_at')->get();
        $dateStr = date('Ymd_His');

        ActivityLogger::log('Penghapusan Aset', 'export', 'Mengekspor laporan rekapitulasi aset dihapus (format ' . strtoupper($format) . ')');

        if ($format === 'pdf') {
            $headers = [
                ['text' => 'NO', 'width' => '3.5%', 'align' => 'center'],
                ['text' => 'TGL HAPUS', 'width' => '9%', 'align' => 'center'],
                ['text' => 'KODE BARANG', 'width' => '12%', 'align' => 'center'],
                ['text' => 'NUP', 'width' => '3.5%', 'align' => 'center'],
                ['text' => 'NAMA BARANG', 'width' => '17.5%', 'align' => 'left'],
                ['text' => 'MERK / TIPE', 'width' => '10.5%', 'align' => 'left'],
                ['text' => 'JENIS', 'width' => '11%', 'align' => 'left'],
                ['text' => 'THN', 'width' => '5%', 'align' => 'center'],
                ['text' => 'NILAI PEROLEHAN (RP)', 'width' => '16%', 'align' => 'right'],
                ['text' => 'ALASAN', 'width' => '12%', 'align' => 'left'],
            ];

            $totalNilai = 0;
            $rows = [];
            foreach ($logs as $i => $item) {
                $nilai = (float)($item->nilai_perolehan ?? 0);
                $totalNilai += $nilai;
                $tglHapus = $item->tanggal_hapus ? \Carbon\Carbon::parse($item->tanggal_hapus)->translatedFormat('d/m/Y') : ($item->deleted_at ? $item->deleted_at->translatedFormat('d/m/Y') : '-');
                $rows[] = [
                    ['text' => $i + 1, 'class' => 'text-center'],
                    ['text' => $tglHapus, 'class' => 'text-center'],
                    ['text' => $item->kode_barang ?? '-', 'class' => 'text-center'],
                    ['text' => $item->nup ?: '1', 'class' => 'text-center'],
                    ['text' => $item->nama_barang ?? '-'],
                    ['text' => $item->merk_type ?? ($item->merk ?? '-')],
                    ['text' => $item->jenis_nama ?? '-'],
                    ['text' => $item->tahun_perolehan ?? '-', 'class' => 'text-center'],
                    ['text' => $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '-', 'class' => 'text-right'],
                    ['text' => $item->alasan_penghapusan ?? '-'],
                ];
            }

            if (!empty($rows)) {
                $rows[] = [
                    ['text' => 'TOTAL MUTASI BERKURANG', 'colspan' => 8, 'style' => 'font-weight:bold;text-align:center;', 'class' => 'text-center'],
                    ['text' => number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'), 'style' => 'font-weight:bold;text-align:right;', 'class' => 'text-right'],
                    ['text' => '', 'style' => 'font-weight:bold;'],
                ];
            }

            $summary = [
                'Total Aset Dihapus' => count($logs) . ' Unit',
                'Total Nilai Mutasi' => 'Rp ' . number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'),
            ];

            return \App\Services\FeatureExportService::downloadPdf(
                'Laporan Rekapitulasi Penghapusan Aset Desa (Mutasi Berkurang)',
                'Daftar Aset Yang Telah Dihapus dari Pembukuan Aset Desa',
                $headers,
                $rows,
                "Rekap_Penghapusan_Aset_{$dateStr}.pdf",
                'landscape',
                $summary
            );
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PenghapusanAsetExport($logs),
            "Rekap_Penghapusan_Aset_{$dateStr}.xlsx"
        );
    }
}

