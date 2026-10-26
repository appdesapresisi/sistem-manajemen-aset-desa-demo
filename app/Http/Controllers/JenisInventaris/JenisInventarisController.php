<?php

namespace App\Http\Controllers\JenisInventaris;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JenisInventarisService;
use App\Services\ActivityLogger;
use App\Models\Notification;
use App\Http\Requests\StoreJenisInventarisRequest;
use App\Http\Requests\UpdateJenisInventarisRequest;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Inventaris;
use App\Models\Pejabat;
use Illuminate\Support\Facades\Log;

class JenisInventarisController extends Controller
{
    protected $service;

    public function __construct(JenisInventarisService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', JenisInventaris::class);
        
        // Build query with filters — exclude virtual jenis (Buku Inventaris Aset Desa)
        // Virtual jenis tidak relevan ditampilkan di sini karena KIB sudah punya tab tersendiri di Export Data
        $query = JenisInventaris::with(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat'])
            ->where('nama', '!=', 'Buku Inventaris Aset Desa');
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'dengan_aset') {
                $query->has('inventaris');
            } elseif ($request->status === 'tanpa_aset') {
                $query->doesntHave('inventaris');
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

        // Paginate with items per page
        $jenisInventarisPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
        // Get total counts
        $allJenis = JenisInventaris::all();
        
        /*
        // Kodingan vs Kemarin (Daily) — disimpan untuk opsi filter di masa depan
        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();

        $jenisToday = JenisInventaris::where('created_at', '>=', $todayStart)->count();
        $jenisYesterday = JenisInventaris::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $jenisDiff = $jenisToday - $jenisYesterday;
        $jenisPercent = $jenisYesterday > 0 ? round(($jenisDiff / $jenisYesterday) * 100, 1) : ($jenisToday > 0 ? 100 : 0);

        $invToday = \App\Models\Inventaris::where('created_at', '>=', $todayStart)->count();
        $invYesterday = \App\Models\Inventaris::whereBetween('created_at', [$yesterdayStart, $todayStart])->count();
        $invDiff = $invToday - $invYesterday;
        $invPercent = $invYesterday > 0 ? round(($invDiff / $invYesterday) * 100, 1) : ($invToday > 0 ? 100 : 0);
        */

        // Konteks Per Bulan (vs. Bulan Lalu)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();

        $jenisThisMonth = JenisInventaris::where('created_at', '>=', $thisMonthStart)->count();
        $jenisLastMonth = JenisInventaris::where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
        $jenisDiff = $jenisThisMonth - $jenisLastMonth;
        $jenisPercent = $jenisLastMonth > 0 ? round(($jenisDiff / $jenisLastMonth) * 100, 1) : ($jenisThisMonth > 0 ? 100 : 0);

        $invThisMonth = \App\Models\Inventaris::where('created_at', '>=', $thisMonthStart)->count();
        $invLastMonth = \App\Models\Inventaris::where('created_at', '>=', $lastMonthStart)->where('created_at', '<', $thisMonthStart)->count();
        $invDiff = $invThisMonth - $invLastMonth;
        $invPercent = $invLastMonth > 0 ? round(($invDiff / $invLastMonth) * 100, 1) : ($invThisMonth > 0 ? 100 : 0);
        $totalInventaris = \App\Models\Inventaris::count();

        $realJenis = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')->get();
        $realJenisCount = $realJenis->count();

        $topJenis = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->withCount('inventaris')
            ->orderBy('inventaris_count', 'desc')
            ->first();
        
        $topJenisNama = $topJenis ? $topJenis->nama : '-';
        $topJenisCount = $topJenis ? $topJenis->inventaris_count : 0;

        $lowestJenis = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->withCount('inventaris')
            ->orderBy('inventaris_count', 'asc')
            ->first();
        
        $lowestJenisNama = $lowestJenis ? $lowestJenis->nama : '-';
        $lowestJenisCount = $lowestJenis ? $lowestJenis->inventaris_count : 0;
        $avgAssetsPerJenis = $realJenisCount > 0 ? round($totalInventaris / $realJenisCount, 1) : 0;

        $stats = [
            'totalJenis'          => $realJenisCount,
            'totalAssets'         => $totalInventaris,
            'topJenisNama'        => $topJenisNama,
            'topJenisCount'       => $topJenisCount,
            'lowestJenisNama'     => $lowestJenisNama,
            'lowestJenisCount'    => $lowestJenisCount,
            'avgAssetsPerJenis'   => $avgAssetsPerJenis,
            'assetsApproved'      => $totalInventaris, // alias untuk kompatibilitas view
            'assetsPending'       => 0,
            'assetsRejected'      => 0,
            'trends' => [
                'jenis'  => ['diff' => $jenisDiff, 'percent' => $jenisPercent],
                'assets' => ['diff' => $invDiff, 'percent' => $invPercent],
            ],
        ];
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            $items = collect($jenisInventarisPaginated->items())->map(function($jenis) {
                if ($jenis->nama === 'Buku Inventaris Aset Desa') {
                    // Get all real jenis IDs
                    $realJenisIds = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')->pluck('id')->toArray();
                    // Hitung semua aset dari real jenis (tanpa filter status)
                    $asetCount = Inventaris::whereIn('jenis_inventaris_id', $realJenisIds)->count();
                } else {
                    // Hitung semua aset per jenis (tanpa filter status)
                    $asetCount = \App\Models\Inventaris::where('jenis_inventaris_id', $jenis->id)->count();
                }
                
                return [
                    'id'          => $jenis->id,
                    'nama'        => $jenis->nama,
                    'deskripsi'   => $jenis->deskripsi ?? '',
                    'icon'        => $jenis->icon ?? 'box',
                    'color_css'   => $jenis->getColorGradientCss(),
                    'aset_count'  => $asetCount,
                    'is_virtual'  => $jenis->isVirtual(),
                ];
            });
            return response()->json([
                'jenisInventaris' => $items,
                'stats'           => $stats,
                'total'           => $jenisInventarisPaginated->total(),
                'from'            => $jenisInventarisPaginated->firstItem() ?? 0,
                'to'              => $jenisInventarisPaginated->lastItem() ?? 0,
                'pagination'      => [
                    'firstItem'   => $jenisInventarisPaginated->firstItem() ?? 0,
                    'lastItem'    => $jenisInventarisPaginated->lastItem() ?? 0,
                    'currentPage' => $jenisInventarisPaginated->currentPage(),
                    'lastPage'    => $jenisInventarisPaginated->lastPage(),
                    'perPage'     => $jenisInventarisPaginated->perPage(),
                    'total'       => $jenisInventarisPaginated->total(),
                ],
            ]);
        }
        
        return view('jenis-inventaris.index', [
            'jenisInventaris' => $jenisInventarisPaginated,
            'stats'           => $stats,
            'virtualAsetCount' => $totalInventaris,
        ]);
    }

    public function create()
    {
        $this->authorize('create', JenisInventaris::class);

        $pejabats = Pejabat::where('is_aktif', 1)->orderBy('nama')->get();
        
        // Transform data untuk JavaScript
        $pejabatsData = $pejabats->map(function($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'tugas' => $p->tugas ?? ''
            ];
        });
        
        // Fixed fields template dari Model
        $fixedFieldsTemplate = collect(JenisInventarisData::AVAILABLE_FIXED_FIELDS)
            ->map(function($config, $fieldName) {
                return [
                    'id' => $fieldName,
                    'fixed_name' => $fieldName,
                    'label' => $config['label'],
                    'type' => 'fixed',
                    'order' => $config['order'] ?? 999
                ];
            })->values();
        
        return view('jenis-inventaris.create', [
            'pejabats' => $pejabats,
            'pejabatsData' => $pejabatsData,
            'fixedFieldsTemplate' => $fixedFieldsTemplate,
            'dropdownList' => \App\Models\Dropdown::with('items')->orderBy('nama')->get(),
        ]);
    }

    public function store(StoreJenisInventarisRequest $request)
    {
        $this->authorize('create', JenisInventaris::class);
        try {
            $data = $request->validated();

            /* Log::debug('JenisInventaris.store', [
                'has_fixed_fields' => $request->has('fixed_fields'),
                'fixed_fields_count' => $request->has('fixed_fields') ? count($request->input('fixed_fields', [])) : 0,
            ]); */

            // Handle enabled fixed fields dengan order dari form
            // Format: fixed_fields[kode_barang] = 1, fixed_fields[nama_barang] = 2
            if ($request->has('fixed_fields')) {
                $data['fixed_fields'] = $request->input('fixed_fields');
            }
            
            $jenis = $this->service->create($data);
            ActivityLogger::log('JenisInventaris', 'tambah', 'Menambahkan jenis inventaris ' . ($data['nama'] ?? '-'), $jenis?->id, $data['nama'] ?? null);

            // Notifikasi ke semua user: jenis inventaris baru
            $namaAktor = auth()->user()->nama ?? auth()->user()->name ?? 'Admin';
            Notification::sendToAll(
                'jenis_inventaris_created',
                'Jenis inventaris baru ditambahkan',
                $namaAktor . ' menambahkan jenis inventaris "' . ($data['nama'] ?? '-') . '" ke sistem.',
                ['jenis_inventaris_id' => $jenis?->id, 'actor_id' => auth()->id()],
                route('jenis-inventaris.show', $jenis?->id)
            );

            return redirect()->route('jenis-inventaris.index')->with('success', 'Data jenis inventaris berhasil ditambahkan ke sistem!');
        } catch (\Exception $e) {
            Log::error('Web store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis inventaris. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $jenis = $this->service->getById($id);
        if (!$jenis) return redirect()->route('jenis-inventaris.index')->withErrors('Jenis inventaris tidak ditemukan');
        $this->authorize('view', $jenis);
        
        // Handle virtual jenis: Buku Inventaris Aset Desa
        if ($jenis->isVirtual()) {
            // PAKEM Buku Inventaris Aset Desa = 8 label tetap (hardcoded)
            // Sesuai dengan struktur export Buku Inventaris Aset Desa
            $bukuInventarisPakem = collect([
                (object) [
                    'id' => 'buku_1',
                    'label_field' => 'Jenis Barang',
                    'field_type' => 'custom',
                    'order' => 1
                ],
                (object) [
                    'id' => 'buku_2',
                    'label_field' => 'Kode Barang',
                    'field_type' => 'custom',
                    'order' => 2
                ],
                (object) [
                    'id' => 'buku_3',
                    'label_field' => 'Nama / Identitas Barang',
                    'field_type' => 'custom',
                    'order' => 3
                ],
                (object) [
                    'id' => 'buku_4',
                    'label_field' => 'Asal Usul Barang',
                    'field_type' => 'custom',
                    'order' => 4,
                    'description' => 'APBDes / Perolehan Lain yang Sah / Kekayaan Asli Desa'
                ],
                (object) [
                    'id' => 'buku_5',
                    'label_field' => 'Tahun Perolehan',
                    'field_type' => 'custom',
                    'order' => 5
                ],
                (object) [
                    'id' => 'buku_6',
                    'label_field' => 'Nilai Aset (Rp)',
                    'field_type' => 'custom',
                    'order' => 6
                ],
                (object) [
                    'id' => 'buku_7',
                    'label_field' => 'Pengguna',
                    'field_type' => 'custom',
                    'order' => 7
                ],
                (object) [
                    'id' => 'buku_8',
                    'label_field' => 'Keterangan',
                    'field_type' => 'custom',
                    'order' => 8
                ]
            ]);
            
            // Query count: semua inventaris dari real jenis (tanpa filter status)
            $realJenisIds = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')->pluck('id')->toArray();
            $approvedCount = Inventaris::whereIn('jenis_inventaris_id', $realJenisIds)
                                       ->count();
            
            // Update deskripsi untuk tampilan (append catatan)
            $jenis->deskripsi = ($jenis->deskripsi ?? 'Rekap terpadu semua jenis aset dengan tanda tangan resmi');
            
            return view('jenis-inventaris.show', [
                'jenis' => $jenis->load('pejabatDefaults.pejabat'),
                'fields' => $bukuInventarisPakem,
                'isVirtual' => true,
                'sourceJenisCount' => count($realJenisIds),
                'approvedCount' => $approvedCount
            ]);
        }
        
        // Normal real jenis - hitung semua aset untuk jenis ini (tanpa filter status)
        $approvedCount = Inventaris::where('jenis_inventaris_id', $jenis->id)
                                   ->count();
        
        return view('jenis-inventaris.show', [
            'jenis' => $jenis->load('fields', 'pejabatDefaults.pejabat'),
            'fields' => $jenis->fields,
            'isVirtual' => false,
            'approvedCount' => $approvedCount
        ]);
    }

    public function edit($id)
    {
        $jenis = $this->service->getById($id);
        if (!$jenis) return redirect()->route('jenis-inventaris.index')->withErrors('Jenis inventaris tidak ditemukan');
        $this->authorize('update', $jenis);
        
        $pejabats = Pejabat::where('is_aktif', 1)->orderBy('nama')->get();
        
        // Transform data untuk JavaScript
        $pejabatsData = $pejabats->map(function($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'tugas' => $p->tugas ?? ''
            ];
        });
        
        // Pre-selected pejabats
        $preSelectedData = $jenis->pejabatDefaults ? $jenis->pejabatDefaults->map(function($p) {
            return [
                'id' => $p->pejabat_id,
                'order' => $p->order
            ];
        })->sortBy('order')->values() : collect();
        
        // Untuk virtual jenis: tidak ada field customization
        if ($jenis->isVirtual()) {
            return view('jenis-inventaris.edit', [
                'jenis' => $jenis,
                'pejabats' => $pejabats,
                'pejabatsData' => $pejabatsData,
                'preSelectedData' => $preSelectedData,
                'fixedFieldsTemplate' => collect(),
                'isVirtual' => true
            ]);
        }

        // Untuk jenis template "Inventaris yang tidak ditemukan" (LHI):
        // field pakem terkunci — user hanya bisa atur deskripsi, icon, pejabat,
        // urutan field, dan tandai field wajib. Tidak ada tambah/hapus field,
        // tidak ada requires_ruangan, tidak ada dropdown.
        if ($jenis->isTidakDitemukan()) {
            // Untuk LHI, kita tidak pass fixedFieldsTemplate dari AVAILABLE_FIXED_FIELDS
            // karena field sudah terkunci di database. JavaScript akan load dari existingFields.
            // Namun, jika belum ada field di DB (edge case), kita fallback ke field pakem LHI.
            $lhiFieldsPakem = [
                ['id' => 'nama_barang', 'fixed_name' => 'nama_barang', 'label' => 'Nama Barang', 'type' => 'fixed', 'order' => 1],
                ['id' => 'kode_barang', 'fixed_name' => 'kode_barang', 'label' => 'Kode Barang', 'type' => 'fixed', 'order' => 2],
                ['id' => 'nup', 'fixed_name' => 'nup', 'label' => 'NUP', 'type' => 'fixed', 'order' => 3],
                ['id' => 'merk_type', 'fixed_name' => 'merk_type', 'label' => 'Merk/Type', 'type' => 'fixed', 'order' => 4],
                ['id' => 'tahun_perolehan', 'fixed_name' => 'tahun_perolehan', 'label' => 'Tahun Perolehan', 'type' => 'fixed', 'order' => 5],
                ['id' => 'nilai_perolehan', 'fixed_name' => 'nilai_perolehan', 'label' => 'Nilai Perolehan', 'type' => 'fixed', 'order' => 6],
                ['id' => 'keterangan', 'fixed_name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'fixed', 'order' => 7],
            ];
            
            return view('jenis-inventaris.edit', [
                'jenis' => $jenis,
                'pejabats' => $pejabats,
                'pejabatsData' => $pejabatsData,
                'preSelectedData' => $preSelectedData,
                'fixedFieldsTemplate' => collect($lhiFieldsPakem),
                'isVirtual' => false,
                'isTidakDitemukan' => true,
                'dropdownList' => collect(),
            ]);
        }

        // Normal real jenis: bisa customize fields
        // Get existing fixed field names from database
        // Filter out jenis_barang — sudah digabung ke nama_barang sebagai "Jenis Barang / Nama Barang"
        $existingFixedFieldNames = $jenis->fields->where('field_type', 'fixed')->pluck('fixed_field_name')->toArray();
        
        // Fixed fields template ONLY for fields not yet selected (disabled fields)
        $fixedFieldsTemplate = collect(JenisInventarisData::AVAILABLE_FIXED_FIELDS)
            ->filter(function($config, $fieldName) use ($existingFixedFieldNames) {
                // Only include fields NOT in database (belum dipilih user)
                return !in_array($fieldName, $existingFixedFieldNames);
            })
            ->map(function($config, $fieldName) {
                return [
                    'id' => $fieldName,
                    'fixed_name' => $fieldName,
                    'label' => $config['label'],
                    'type' => 'fixed',
                    'order' => $config['order'] ?? 999
                ];
            })->values();
        
        return view('jenis-inventaris.edit', [
            'jenis' => $jenis,
            'pejabats' => $pejabats,
            'pejabatsData' => $pejabatsData,
            'preSelectedData' => $preSelectedData,
            'fixedFieldsTemplate' => $fixedFieldsTemplate,
            'isVirtual' => false,
            'dropdownList' => \App\Models\Dropdown::with('items')->orderBy('nama')->get(),
        ]);
    }

    public function update(UpdateJenisInventarisRequest $request, $id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('jenis-inventaris.index')->with('error', 'Jenis inventaris tidak ditemukan.');
        $this->authorize('update', $existing);

        try {
            $data = $request->validated();
            
            /* Log::debug('JenisInventaris.update', [
                'id' => $id,
                'nama' => $data['nama'] ?? null,
                'fields_count' => isset($data['fields']) ? count($data['fields']) : 0,
                'fixed_fields_count' => isset($data['fixed_fields']) ? count($data['fixed_fields']) : 0,
            ]); */

            // Handle enabled fixed fields dengan order dari form
            if ($request->has('fixed_fields')) {
                $data['fixed_fields'] = $request->input('fixed_fields');
            }

            $this->service->update($id, $data);
            ActivityLogger::log('JenisInventaris', 'ubah', 'Mengubah jenis inventaris ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            return redirect()->route('jenis-inventaris.index')->with('success', 'Data jenis inventaris berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Web update error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal memperbarui jenis inventaris. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('jenis-inventaris.index')->with('error', 'Jenis inventaris tidak ditemukan.');
        
        // Prevent delete Buku Inventaris Aset Desa (virtual jenis)
        if ($existing->isVirtual()) {
            return redirect()->route('jenis-inventaris.show', $existing->id)
                ->with('error', '❌ Buku Inventaris Aset Desa tidak boleh dihapus. Hanya pejabat yang dapat diubah.');
        }

        // Prevent delete template sistem pakem LHI (Inventaris yang tidak ditemukan)
        if ($existing->isTidakDitemukan()) {
            return redirect()->route('jenis-inventaris.show', $existing->id)
                ->with('error', '❌ Jenis template LHI "Inventaris yang tidak ditemukan" adalah master sistem pakem dan tidak boleh dihapus.');
        }
        
        $this->authorize('delete', $existing);

        try {
            $this->service->delete($id);
            ActivityLogger::log('JenisInventaris', 'hapus', 'Menghapus jenis inventaris ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            return redirect()->route('jenis-inventaris.index')->with('success', 'Data jenis inventaris berhasil dihapus dari sistem!');
        } catch (\Exception $e) {
            Log::error('Web delete error', ['id' => $id, 'error' => $e->getMessage()]);
            // Tampilkan pesan error spesifik dari service (misalnya FK constraint)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        // Authorize sekali di awal — policy delete JenisInventaris hanya untuk super_admin.
        // Tidak perlu per-item karena tidak ada ownership (bukan "hapus milik sendiri").
        $this->authorize('delete', JenisInventaris::class);

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
        $skipped = [];
        foreach ($ids as $id) {
            $jenis = $this->service->getById($id);
            if (!$jenis) continue;

            // Proteksi: Buku Inventaris Aset Desa tidak boleh dihapus
            if ($jenis->isVirtual()) {
                $skipped[] = $jenis->nama . ' (tidak dapat dihapus)';
                continue;
            }

            // Proteksi: Jenis template LHI tidak boleh dihapus
            if ($jenis->isTidakDitemukan()) {
                $skipped[] = $jenis->nama . ' (template sistem pakem tidak dapat dihapus)';
                continue;
            }

            try {
                $this->service->delete($id);
                ActivityLogger::log('JenisInventaris', 'hapus', 'Menghapus jenis inventaris ' . ($jenis->nama ?? '-'), (int) $id, $jenis->nama ?? null);
                $deleted++;
            } catch (\Exception $e) {
                $skipped[] = $jenis->nama ?? $id;
            }
        }

        $msg = $deleted . ' jenis inventaris berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Tidak dapat dihapus: ' . implode(', ', $skipped) . '.';
        }
        if ($totalRequested > 100) {
            $msg .= ' ' . ($totalRequested - 100) . ' data tersisa, pilih ulang untuk menghapus selanjutnya.';
        }
        return redirect()->route('jenis-inventaris.index')->with($deleted > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Cek berapa aset yang menggunakan field tertentu (untuk warning sebelum hapus/disable).
     * GET /api/jenis-inventaris-field/{fieldId}/usage
     */
    public function fieldUsage(int $fieldId)
    {
        $field = \App\Models\JenisInventarisData::find($fieldId);
        if (!$field) {
            return response()->json(['count' => 0, 'label' => '']);
        }

        $count = \App\Models\InventarisValue::where('jenis_inventaris_data_id', $fieldId)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->where('value', '!=', '-')
            ->count();

        return response()->json([
            'count' => $count,
            'label' => $field->label_field ?? $field->nama_field ?? '',
        ]);
    }
}
