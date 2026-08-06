<?php

namespace App\Http\Controllers\ExportData;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetPejabatOverrideRequest;
use App\Http\Requests\StoreExportRequest;
use App\Http\Requests\StoreAllExportRequest;
use App\Models\ExportLog;
use App\Services\ExportService;
use App\Models\VirtualJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ExportLog::class);

        // Load jenis inventaris dengan count inventaris (REAL jenis dari DB)
        $allJenis = \App\Models\JenisInventaris::withCount('inventaris')
            ->with(['fields' => function($q){ $q->orderBy('order','asc'); }, 'pejabatDefaults'])
            ->get();

        // Separate Buku Inventaris (virtual) dari real jenis
        $bukuInventaris  = $allJenis->firstWhere('nama', 'Buku Inventaris Aset Desa');
        $jenisInventaris = $allJenis->where('nama', '!=', 'Buku Inventaris Aset Desa');

        // Count total inventaris (untuk Buku Inventaris)
        $totalApprovedInventaris = \App\Models\Inventaris::count();

        // Catatan: jenis "Inventaris yang tidak ditemukan" kini berupa jenis nyata (template LHI),
        // sudah otomatis termasuk di $jenisInventaris. Konsep pseudo-orphan dihapus.

        // Load virtual jenis dari sistem (untuk future use)
        $virtualJenis = VirtualJenis::all();

        // Load pejabat untuk custom selection
        $pejabats = \App\Models\Pejabat::where('is_aktif', 1)->get();

        // Tentukan pejabat "tetap" yang tidak boleh dipilih user di modal
        // Format 2 KIR: Perbekel adalah tetap
        $perbekelPejabat = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%perbekel%'])->first();

        // Format 1 KIR & KIB: Sekretaris Desa adalah tetap
        // Sekretaris ditentukan dari user dengan role sekretaris yang punya pejabat
        $sekretarisUser = \App\Models\User::with('pejabat')
            ->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))
            ->first();
        $sekretarisPejabat = $sekretarisUser?->pejabat ?? null;

        // Kumpulkan ID pejabat tetap per konteks
        $fixedPejabatIds = [
            'kir_format1' => $sekretarisPejabat ? [$sekretarisPejabat->id] : [],   // Sekretaris Desa tetap di KIR Format 1
            'kir_format2' => $perbekelPejabat   ? [$perbekelPejabat->id]   : [],   // Perbekel tetap di KIR Format 2
            'kib'         => $sekretarisPejabat ? [$sekretarisPejabat->id] : [],   // Sekretaris Desa tetap di KIB
        ];

        // Load ruangan untuk tab export ruangan
        $ruanganList = \App\Models\Ruangan::withCount('inventaris')->orderBy('nama')->get();

        // KIB types untuk tab export KIB
        $kibTypes = collect(config('kib'))->map(fn($v, $k) => [
            'type'  => $k,
            'nama'  => $v['nama'],
            'judul' => $v['judul_export'],
            'count' => \App\Models\Inventaris::whereHas('jenis', fn($q) => $q->where('kib_type', $k))->count(),
        ])->values();

        return view('export-data.index', compact('jenisInventaris', 'virtualJenis', 'pejabats', 'fixedPejabatIds', 'bukuInventaris', 'totalApprovedInventaris', 'ruanganList', 'kibTypes'));
    }

    public function store(StoreExportRequest $request)
    {
        $this->authorize('create', ExportLog::class);

        $data = $request->validated();

        $jenisIdRaw        = $data['jenis_inventaris_id'];
        $format            = $data['format'];
        $tahun             = $data['tahun'] ?? null;
        $signatureOption   = $data['signature_option'] ?? 'with';
        $pejabatOverride   = $data['pejabat_override'] ?? [];
        $tanggalDokumen    = (string) ($data['tanggal_dokumen'] ?? '');
        $keteranganDokumen = (string) ($data['keterangan_dokumen'] ?? '');
        $paperSize         = (string) ($data['paper_size'] ?? 'legal');
        // Informasi Dokumen (Lampiran & Berita Acara)
        $docLampiran       = (string) ($data['doc_lampiran'] ?? '');
        $docBaNomor        = (string) ($data['doc_ba_nomor'] ?? '');
        $docBaTanggal      = (string) ($data['doc_ba_tanggal'] ?? '');

        try {
            $service = app(ExportService::class);

            // Pastikan ID numerik sebelum lanjut
            if (!is_numeric($jenisIdRaw)) {
                return response()->json(['success' => false, 'message' => 'Jenis tidak valid'], 400);
            }

            // Cek via Model apakah ini Buku Inventaris Aset Desa (virtual)
            $jenis = \App\Models\JenisInventaris::findOrFail($jenisIdRaw);
            $isVirtual = $jenis->isVirtual();

            if ($isVirtual) {
                $result = $service->exportBukuInventarisStandalone([
                    'format'             => $format,
                    'filters'            => ['tahun' => $tahun ?? null],
                    'signature_option'   => $signatureOption,
                    'pejabat_override'   => !empty($pejabatOverride) ? $pejabatOverride : null,
                    'tanggal_dokumen'    => $tanggalDokumen,
                    'keterangan_dokumen' => $keteranganDokumen,
                    'paper_size'         => $paperSize,
                    'doc_lampiran'       => $docLampiran,
                    'doc_ba_nomor'       => $docBaNomor,
                    'doc_ba_tanggal'     => $docBaTanggal,
                    'user_id'            => Auth::id(),
                ]);
            } else {
                $result = $service->exportJenisInventaris([
                    'jenis_inventaris_id' => $jenis->id,
                    'format'              => $format,
                    'filters'             => ['tahun' => $tahun ?? null],
                    'signature_option'    => $signatureOption,
                    'pejabat_override'    => !empty($pejabatOverride) ? $pejabatOverride : null,
                    'tanggal_dokumen'     => $tanggalDokumen,
                    'keterangan_dokumen'  => $keteranganDokumen,
                    'paper_size'          => $paperSize,
                    'doc_lampiran'        => $docLampiran,
                    'doc_ba_nomor'        => $docBaNomor,
                    'doc_ba_tanggal'      => $docBaTanggal,
                    'user_id'             => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Export berhasil',
                'filename' => $result['filename'] ?? 'export',
                'file_url' => route('export-data.download', ['filename' => $result['filename'] ?? 'export'], false),
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error', ['message' => $e->getMessage(), 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Export gagal. Silakan coba lagi atau hubungi administrator.',
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        /** @var \App\Models\User|null $user */
        $user = $request->user();
        $canDelete = $user && $user->isSuperAdmin();

        $sort  = $request->input('sort', 'terbaru');
        $query = \App\Models\ExportLog::with(['user', 'jenisInventaris', 'pejabat'])
            ->whereIn('tipe_export', ['excel', 'pdf']);

        match ($sort) {
            'terlama'   => $query->orderBy('created_at', 'asc'),
            'user_asc'  => $query->leftJoin('users', 'export_logs.user_id', '=', 'users.id')->orderBy('users.nama', 'asc')->select('export_logs.*'),
            'user_desc' => $query->leftJoin('users', 'export_logs.user_id', '=', 'users.id')->orderBy('users.nama', 'desc')->select('export_logs.*'),
            default     => $query->orderBy('created_at', 'desc'),
        };

        // Search: by user name, jenis name, or filename
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('jenisInventaris', fn($j) => $j->where('nama', 'like', "%{$search}%"))
                  ->orWhere('nama_file', 'like', "%{$search}%");
            });
        }

        // Filter by format (supports multi-select checkbox format[])
        $selectedFormats = array_values(array_filter((array) $request->input('format', [])));
        if (empty($selectedFormats) && $request->filled('format')) {
            $selectedFormats = [strtolower((string) $request->input('format'))];
        }
        if (!empty($selectedFormats)) {
            $query->whereIn('tipe_export', array_map('strtolower', $selectedFormats));
        }

        // Filter by jenis inventaris (supports multi-select checkbox jenis[])
        $selectedJenis = array_values(array_filter((array) $request->input('jenis', [])));
        if (empty($selectedJenis) && $request->filled('jenis')) {
            $selectedJenis = [(string) $request->input('jenis')];
        }
        if (!empty($selectedJenis)) {
            $selectedJenis = array_map('strval', $selectedJenis);
            $includeAllJenis = in_array('all', $selectedJenis, true);
            $selectedJenisIds = array_values(array_filter($selectedJenis, fn($v) => $v !== 'all'));

            $query->where(function ($q) use ($includeAllJenis, $selectedJenisIds) {
                if (!empty($selectedJenisIds)) {
                    $q->whereIn('jenis_inventaris_id', $selectedJenisIds);
                }

                if ($includeAllJenis) {
                    if (!empty($selectedJenisIds)) {
                        $q->orWhereNull('jenis_inventaris_id');
                    } else {
                        $q->whereNull('jenis_inventaris_id');
                    }
                }
            });
        }

        // Filter by quick date range preset
        if ($request->filled('date_range')) {
            $today = now()->endOfDay();
            $start = null;

            if ($request->date_range === '1d') {
                $start = now()->subDay()->startOfDay();
            } elseif ($request->date_range === '7d') {
                $start = now()->subDays(7)->startOfDay();
            } elseif ($request->date_range === '30d') {
                $start = now()->subDays(30)->startOfDay();
            }

            if ($start) {
                $query->whereBetween('created_at', [$start, $today]);
            }
        }

        // Backward compatibility for old explicit date range params
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $logs      = $query->paginate($perPage)->withQueryString();
        $jenisList = \App\Models\JenisInventaris::orderBy('nama')->get(['id', 'nama']);

        return view('export-data.history', compact('logs', 'jenisList', 'canDelete'));
    }

    public function destroyHistory(Request $request, ExportLog $log)
    {
        $this->abortIfNotSuperAdmin($request);

        $log->delete();

        return redirect()->back()->with('success', 'Data riwayat export berhasil dihapus.');
    }

    public function destroyAllHistory(Request $request)
    {
        $this->abortIfNotSuperAdmin($request);

        $deleted = ExportLog::query()->delete();
        $message = $deleted > 0
            ? 'Semua data riwayat export berhasil dihapus.'
            : 'Tidak ada data riwayat export yang perlu dihapus.';

        return redirect()->back()->with('success', $message);
    }

    public function download(string $filename)
    {
        $log = \App\Models\ExportLog::where('nama_file', $filename)->firstOrFail();

        // Protected download: only owner/admin based on policy.
        $this->authorize('view', $log);

        $storage = Storage::disk('local');
        $path = 'exports/' . $log->created_at->format('Y/m') . '/' . $filename;

        // Fallback search to handle files not found in expected month folder.
        if (!$storage->exists($path)) {
            $path = null;
            foreach ($storage->allFiles('exports') as $file) {
                if (basename($file) === $filename) {
                    $path = $file;
                    break;
                }
            }
        }

        if (!$path || !$storage->exists($path)) {
            abort(404, 'File not found');
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf'  => 'application/pdf',
            'html' => 'text/html',
        ][$ext] ?? 'application/octet-stream';

        return response()->streamDownload(function () use ($storage, $path) {
            echo $storage->get($path);
        }, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function getRuanganPejabatInfo(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        $kirFormat = $request->input('kir_format', 'standard');

        if ($kirFormat === 'custom') {
            // Format 2 (Desa Demo): Perbekel + Kasi Kesejahteraan + Pengurus Barang
            $perbekel = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%perbekel%'])->first();
            $perbekelData = $perbekel ? ['nama' => $perbekel->nama, 'jabatan' => $perbekel->jabatan] : null;

            // Kasi Kesejahteraan — sama seperti resolveKasiKesejahteraan di ExportService
            $kasiData = null;
            $ruanganId = $request->input('ruangan_id');
            if ($ruanganId === 'all') {
                $kasiData = [
                    'nama' => '(Penanggung Jawab Masing-masing Ruangan)',
                    'jabatan' => 'Disesuaikan per Ruangan'
                ];
            } elseif ($ruanganId) {
                $ruanganObj = \App\Models\Ruangan::with('penanggungJawab.pejabat')->find($ruanganId);
                if ($ruanganObj && $ruanganObj->penanggungJawab) {
                    $pjUser = $ruanganObj->penanggungJawab;
                    if ($pjUser->pejabat) {
                        $kasiData = [
                            'nama' => $pjUser->pejabat->nama,
                            'jabatan' => $pjUser->pejabat->jabatan
                        ];
                    } else {
                        $kasiData = [
                            'nama' => $pjUser->nama,
                            'jabatan' => $pjUser->role_name_display ?: 'Penanggungjawab Ruangan'
                        ];
                    }
                }
            }

            if (!$kasiData) {
                $kasiPejabat = \App\Models\Pejabat::whereRaw("LOWER(jabatan) LIKE ?", ['%kasi kesejahteraan%'])->first();
                if ($kasiPejabat) {
                    $kasiData = ['nama' => $kasiPejabat->nama, 'jabatan' => $kasiPejabat->jabatan];
                } else {
                    $kasiUser = \App\Models\User::with('pejabat')
                        ->whereHas('role', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kasi%']))
                        ->whereHas('bidang', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kesejahteraan%']))
                        ->first();
                    if ($kasiUser?->pejabat) {
                        $kasiData = ['nama' => $kasiUser->pejabat->nama, 'jabatan' => $kasiUser->pejabat->jabatan];
                    } else {
                        $kasiUser2 = \App\Models\User::with('pejabat')
                            ->whereHas('role', fn($q) => $q->whereRaw("LOWER(nama) LIKE ?", ['%kasi%']))
                            ->whereNotNull('pejabat_id')
                            ->first();
                        if ($kasiUser2?->pejabat) {
                            $kasiData = ['nama' => $kasiUser2->pejabat->nama, 'jabatan' => $kasiUser2->pejabat->jabatan];
                        }
                    }
                }
            }

            $petugasBarang = \App\Models\Pejabat::where('is_petugas_barang', 1)->first();
            $petugasData = $petugasBarang ? ['nama' => $petugasBarang->nama, 'jabatan' => $petugasBarang->jabatan] : null;

            return response()->json([
                'kir_format'  => 'custom',
                'perbekel'    => $perbekelData,
                'kasi'        => $kasiData,
                'petugas'     => $petugasData,
            ]);
        }

        // Format 1 (Standar Permendagri): Sekretaris Desa + Petugas Barang
        $sekretarisUser = \App\Models\User::with(['role', 'pejabat'])
            ->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))
            ->first();

        $sekretaris = null;
        if ($sekretarisUser && $sekretarisUser->pejabat) {
            $sekretaris = [
                'nama'    => $sekretarisUser->pejabat->nama,
                'jabatan' => $sekretarisUser->pejabat->jabatan,
            ];
        } elseif ($sekretarisUser) {
            $sekretaris = [
                'nama'    => $sekretarisUser->nama,
                'jabatan' => 'Sekretaris Desa',
            ];
        }

        $petugasBarang = \App\Models\Pejabat::where('is_petugas_barang', 1)->first();
        $petugas = $petugasBarang ? [
            'nama'    => $petugasBarang->nama,
            'jabatan' => $petugasBarang->jabatan,
        ] : null;

        return response()->json([
            'kir_format'  => 'standard',
            'sekretaris'  => $sekretaris,
            'petugas'     => $petugas,
        ]);
    }

    public function getRuanganCount(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        $ruanganId = $request->input('ruangan_id');
        $tahun     = $request->input('tahun');

        $query = \App\Models\Inventaris::whereNotNull('ruangan_id');

        if ($ruanganId !== 'all' && $ruanganId) {
            $query->where('ruangan_id', (int)$ruanganId);
        }

        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        return response()->json(['count' => $query->count()]);
    }

    public function getDataCount(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        $jenisId = $request->input('jenis_inventaris_id');

        // 'all' = hitung total seluruh jenis
        if ($jenisId === 'all') {
            $query = \App\Models\Inventaris::query();
            if ($request->filled('tahun')) {
                $query->whereYear('created_at', $request->tahun);
            }
            return response()->json(['count' => $query->count()]);
        }

        $jenisId = (int) $jenisId;
        if (!$jenisId) {
            return response()->json(['count' => 0]);
        }

        // Jika virtual (Buku Inventaris Aset Desa), hitung semua inventaris
        $jenis = \App\Models\JenisInventaris::find($jenisId);
        if ($jenis && $jenis->isVirtual()) {
            $query = \App\Models\Inventaris::query();
            if ($request->filled('tahun')) {
                $query->whereYear('created_at', $request->tahun);
            }
            return response()->json(['count' => $query->count()]);
        }

        $query = \App\Models\Inventaris::where('jenis_inventaris_id', $jenisId);
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }
        return response()->json(['count' => $query->count()]);
    }

    public function getHistoryJson(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        $jenisId = $request->integer('jenis_inventaris_id');
        $query = \App\Models\ExportLog::with(['user:id,nama', 'jenisInventaris:id,nama', 'pejabat:id,nama'])
            ->whereIn('tipe_export', ['excel', 'pdf'])
            ->orderBy('created_at', 'desc')
            ->limit(5);
        if ($jenisId) {
            $query->where('jenis_inventaris_id', $jenisId);
        }
        $logs = $query->get()->map(function ($log) {
            $userName   = $log->user->nama ?? 'Unknown';
            $initial    = strtoupper(mb_substr($userName, 0, 1));

            // TTD: ikuti mode signature_option saat export
            $filters = is_array($log->filters) ? $log->filters : [];
            $sigOpt = strtolower((string) ($filters['signature_option'] ?? ''));
            $override = $log->pejabat_override;
            if ($sigOpt === 'without') {
                $ttd = 'Tanpa Tanda Tangan';
            } elseif (is_array($override) && count($override) > 0) {
                $names = collect($override)
                    ->map(function ($p) {
                        if (!is_array($p)) return null;
                        return $p['nama'] ?? ($p['nama_pejabat'] ?? null);
                    })
                    ->filter()
                    ->values();

                if ($names->count() > 0) {
                    $ttd = $names->count() > 1
                        ? ($names->first() . ' +' . ($names->count() - 1) . ' lainnya')
                        : $names->first();
                } else {
                    $ttd = count($override) . ' Pejabat';
                }
            } elseif ($log->pejabat_id) {
                $ttd = $log->pejabat->nama ?? '1 Pejabat';
            } else {
                $ttd = 'Tidak';
            }

            // Jumlah: semua jenis jika log all-jenis, jika tidak berdasarkan jenis log
            $jumlahQuery = \App\Models\Inventaris::query();
            if ($log->jenis_inventaris_id) {
                $jumlahQuery->where('jenis_inventaris_id', $log->jenis_inventaris_id);
            }
            if (!empty($filters['tahun'])) {
                $jumlahQuery->whereYear('created_at', $filters['tahun']);
            }
            $jumlah = $jumlahQuery->count();

            return [
                'user'   => $userName,
                'avatar' => $initial,
                'date'   => $log->created_at->format('Y-m-d'),
                'time'   => $log->created_at->format('H:i'),
                'format' => strtoupper($log->tipe_export ?? 'EXCEL'),
                'ttd'    => $ttd,
                'jumlah' => $jumlah,
                'note'   => $log->nama_file ?? '',
            ];
        });
        return response()->json($logs);
    }

    public function setPejabatOverride(SetPejabatOverrideRequest $request)
    {
        $this->authorize('create', ExportLog::class);

        $data = $request->validated();
        $sessionKey = 'export_pejabat_override_' . $data['jenis_inventaris_id'];
        session([$sessionKey => $data['pejabat_override']]);
        return response()->json(['status' => 'ok', 'message' => 'Pejabat override disimpan sementara.']);
    }

    /**
     * Export semua jenis inventaris sekaligus → single file (excel/pdf).
     * POST /export-data/store-all
     */
    public function storeAll(StoreAllExportRequest $request)
    {
        $this->authorize('create', ExportLog::class);

        $data = $request->validated();

        try {
            $service = app(ExportService::class);
            $result  = $service->exportAll([
                'format'             => $data['format'],
                'filters'            => ['tahun' => $data['tahun'] ?? null],
                'signature_option'   => $data['signature_option'] ?? 'with',
                'paper_size'         => $data['paper_size'] ?? 'legal',
                'tanggal_dokumen'    => $data['tanggal_dokumen'] ?? '',
                'keterangan_dokumen' => $data['keterangan_dokumen'] ?? '',
                'pejabat_override'   => !empty($data['pejabat_override']) ? $data['pejabat_override'] : null,
                'user_id'            => Auth::id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Export semua jenis berhasil',
                'filename' => $result['filename'],
                'file_url' => route('export-data.download', ['filename' => $result['filename']], false),
            ]);
        } catch (\Exception $e) {
            Log::error('Export All Error', ['message' => $e->getMessage(), 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Export gagal. Silakan coba lagi atau hubungi administrator.',
            ], 500);
        }
    }

    public function storeRuangan(\Illuminate\Http\Request $request)
    {
        $this->authorize('create', ExportLog::class);

        $request->validate([
            'ruangan_id'         => ['required'],
            'format'             => ['required', 'in:pdf,excel'],
            'tahun'              => ['nullable', 'integer'],
            'signature_option'   => ['nullable', 'in:with,without'],
            'paper_size'         => ['nullable', 'in:legal,letter,f4'],
            'kir_format'         => ['nullable', 'in:standard,custom'],
            'tanggal_dokumen'    => ['nullable', 'date'],
            'keterangan_dokumen' => ['nullable', 'string', 'max:100'],
            'pejabat_override'   => ['nullable', 'array'],
            'pejabat_override.*' => ['integer', 'exists:pejabats,id'],
        ]);

        // Cek apakah ada Sekretaris Desa — hanya diperlukan untuk Format 1 (Standar)
        $kirFormat = $request->input('kir_format', 'standard');
        if ($kirFormat !== 'custom') {
            $sekretarisExists = \App\Models\User::whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists();
            if (!$sekretarisExists) {
                return response()->json([
                    'success'          => false,
                    'no_sekretaris'    => true,
                    'message'          => 'Belum ada user dengan role Sekretaris Desa. Silakan assign role Sekretaris Desa terlebih dahulu sebelum export Kartu Inventaris Ruangan.',
                ], 422);
            }
        }
        $ruanganId         = $request->input('ruangan_id');
        $format            = $request->input('format', 'pdf');
        $tahun             = $request->input('tahun');
        $signatureOption   = $request->input('signature_option', 'with');
        $pejabatOverride   = $request->input('pejabat_override', []);
        $tanggalDokumen    = $request->input('tanggal_dokumen', '');
        $keteranganDokumen = $request->input('keterangan_dokumen', '');
        $paperSize         = $request->input('paper_size', 'legal');
        // $kirFormat sudah di-set di atas saat pengecekan Sekretaris

        try {
            $service = app(ExportService::class);
            $result  = $service->exportRuangan([
                'ruangan_id'         => $ruanganId === 'all' ? 'all' : (int)$ruanganId,
                'format'             => $format,
                'kir_format'         => $kirFormat,
                'filters'            => ['tahun' => $tahun],
                'signature_option'   => $signatureOption,
                'pejabat_override'   => !empty($pejabatOverride) ? $pejabatOverride : [],
                'tanggal_dokumen'    => $tanggalDokumen,
                'keterangan_dokumen' => $keteranganDokumen,
                'paper_size'         => $paperSize,
                'user_id'            => Auth::id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Export Kartu Inventaris Ruangan berhasil',
                'filename' => $result['filename'],
                'file_url' => route('export-data.download', ['filename' => $result['filename']], false),
            ]);
        } catch (\Exception $e) {
            Log::error('Export Ruangan Error', ['message' => $e->getMessage(), 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Export gagal. Silakan coba lagi atau hubungi administrator.',
            ], 500);
        }
    }

    private function abortIfNotSuperAdmin(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        abort_unless($user && $user->isSuperAdmin(), 403);
    }

    /**
     * Export KIB (Kartu Inventaris Barang) — A, B, C, D, E, F, atau semua.
     * POST /export-data/store-kib
     */
    public function storeKib(Request $request)
    {
        $this->authorize('create', ExportLog::class);

        $request->validate([
            'kib_type'           => ['required', 'in:A,B,C,D,E,F,all'],
            'format'             => ['required', 'in:pdf,excel'],
            'tahun'              => ['nullable', 'integer'],
            'signature_option'   => ['nullable', 'in:with,without'],
            'paper_size'         => ['nullable', 'in:legal,letter,f4'],
            'pejabat_kiri'       => ['nullable', 'integer', 'exists:pejabats,id'],
            'pejabat_kanan'      => ['nullable', 'integer', 'exists:pejabats,id'],
            'tanggal_dokumen'    => ['nullable', 'string'],
            'keterangan_dokumen' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $service = app(\App\Services\KibExportService::class);
            $result  = $service->export([
                'kib_type'           => $request->input('kib_type'),
                'format'             => $request->input('format', 'pdf'),
                'filters'            => ['tahun' => $request->input('tahun')],
                'signature_option'   => $request->input('signature_option', 'with'),
                'paper_size'         => $request->input('paper_size', 'legal'),
                'pejabat_override'   => [
                    'kiri'  => $request->input('pejabat_kiri'),
                    'kanan' => $request->input('pejabat_kanan'),
                ],
                'tanggal_dokumen'    => $request->input('tanggal_dokumen', ''),
                'keterangan_dokumen' => $request->input('keterangan_dokumen', ''),
                'user_id'            => Auth::id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Export KIB berhasil',
                'filename' => $result['filename'],
                'file_url' => route('export-data.download', ['filename' => $result['filename']], false),
            ]);
        } catch (\Exception $e) {
            Log::error('Export KIB Error', ['message' => $e->getMessage(), 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Export KIB gagal. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Hitung jumlah baris data yang akan muncul di Laporan Aset.
     * GET /export-data/laporan-count?tahun=2025&periode=tahunan
     */
    public function getLaporanCount(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        $tahun   = (int) $request->input('tahun', date('Y'));
        $periode = $request->input('periode', 'tahunan');

        $service = app(\App\Services\LaporanAsetService::class);
        $rows = $service->buildRows($tahun, $periode);

        return response()->json(['count' => count($rows)]);
    }

    /**
     * Export Laporan Aset Per Tahun / Per Semester.
     * POST /export-data/store-laporan-aset
     */
    public function storeLaporanAset(Request $request)
    {
        $this->authorize('create', ExportLog::class);

        $request->validate([
            'tahun'   => ['required', 'integer', 'min:2000', 'max:2100'],
            'periode' => ['required', 'in:tahunan,semester,semester_1,semester_2'],
            'format'  => ['required', 'in:excel,pdf'],
        ]);

        try {
            $service = app(\App\Services\LaporanAsetService::class);
            $result  = $service->export([
                'tahun'   => (int) $request->input('tahun'),
                'periode' => $request->input('periode'),
                'format'  => $request->input('format', 'excel'),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Export Laporan Aset berhasil',
                'filename' => $result['filename'],
                'file_url' => route('export-data.download', ['filename' => $result['filename']], false),
            ]);
        } catch (\Exception $e) {
            Log::error('Export Laporan Aset Error', ['message' => $e->getMessage(), 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Export Laporan Aset gagal. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Ambil info pejabat default untuk tab KIB.
     * GET /export-data/kib-pejabat-info
     */
    public function getKibPejabatInfo(Request $request)
    {
        $this->authorize('viewAny', ExportLog::class);

        // Sekretaris Desa (kiri)
        $sekretarisUser = \App\Models\User::with(['role', 'pejabat'])
            ->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))
            ->first();

        $sekretaris = null;
        if ($sekretarisUser?->pejabat) {
            $sekretaris = ['id' => $sekretarisUser->pejabat->id, 'nama' => $sekretarisUser->pejabat->nama, 'jabatan' => $sekretarisUser->pejabat->jabatan];
        } elseif ($sekretarisUser) {
            $sekretaris = ['id' => null, 'nama' => $sekretarisUser->nama, 'jabatan' => 'Sekretaris Desa'];
        }

        // Petugas Barang (kanan)
        $petugasBarang = \App\Models\Pejabat::where('is_petugas_barang', 1)->first();
        $petugas = $petugasBarang
            ? ['id' => $petugasBarang->id, 'nama' => $petugasBarang->nama, 'jabatan' => $petugasBarang->jabatan]
            : null;

        return response()->json([
            'sekretaris' => $sekretaris,
            'petugas'    => $petugas,
        ]);
    }

}
