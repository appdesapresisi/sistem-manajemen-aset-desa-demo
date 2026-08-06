<?php

namespace App\Http\Controllers\Pejabat;

use App\Http\Controllers\Controller;
use App\Services\PejabatService;
use App\Services\ActivityLogger;
use App\Http\Requests\StorePejabatRequest;
use App\Http\Requests\UpdatePejabatRequest;
use App\Models\Pejabat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class PejabatController extends Controller
{
    protected $service;

    public function __construct(PejabatService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Pejabat::class);
        
        // Build query with filters
        $query = Pejabat::query();
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('jabatan', 'like', '%' . $search . '%')
                  ->orWhere('tugas', 'like', '%' . $search . '%');
            });
        }
        
        // Jabatan / Role filter
        if ($request->has('jabatan') && is_array($request->jabatan) && count($request->jabatan) > 0) {
            $jabatanList = array_filter($request->jabatan);
            if (!empty($jabatanList)) {
                $query->where(function($q) use ($jabatanList) {
                    foreach ($jabatanList as $jab) {
                        $j = strtolower(trim($jab));
                        if ($j === 'super_admin' || $j === 'super admin') {
                            $q->orWhere('jabatan', 'like', '%super%admin%')
                              ->orWhereHas('users.role', function($rq) { $rq->where('nama', 'super_admin'); });
                        } elseif ($j === 'perbekel') {
                            $q->orWhere('jabatan', 'like', '%perbekel%')
                              ->orWhere('jabatan', 'like', '%kepala desa%');
                        } elseif ($j === 'sekretaris_desa' || $j === 'sekretaris desa') {
                            $q->orWhere('jabatan', 'like', '%sekretaris%');
                        } elseif ($j === 'kaur' || $j === 'kepala urusan') {
                            $q->orWhere('jabatan', 'like', '%kaur%');
                        } elseif ($j === 'kasi') {
                            $q->orWhere('jabatan', 'like', '%kasi%');
                        } elseif ($j === 'kelian_banjar' || $j === 'kelian banjar') {
                            $q->orWhere('jabatan', 'like', '%kelian%')
                              ->orWhere('jabatan', 'like', '%banjar%');
                        } elseif ($j === 'staf') {
                            $q->orWhere('jabatan', 'like', '%staf%');
                        } else {
                            $q->orWhere('jabatan', 'like', '%' . $jab . '%');
                        }
                    }
                });
            }
        }

        // Sort order
        $sortBy = $request->get('sort', 'nama_asc');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } elseif ($sortBy === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            // nama_asc (default)
            $query->orderBy('nama', 'asc');
        }
        
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        // Paginate items per page
        $pejabatsPaginated = $query->withCount('users')->paginate($perPage)->appends($request->except('page'));
        
        // Get total counts from all pejabats (not just current page)
        $statsRow = Pejabat::query()
            ->selectRaw('COUNT(*) as total_pejabat')
            ->selectRaw("SUM(CASE WHEN ttd_path IS NOT NULL AND ttd_path <> '' THEN 1 ELSE 0 END) as ttd_lengkap")
            ->selectRaw("COUNT(DISTINCT CASE WHEN jabatan IS NOT NULL AND jabatan <> '' THEN jabatan END) as total_jabatan")
            ->first();

        $stats = [
            'totalPejabat' => (int) ($statsRow->total_pejabat ?? 0),
            'ttdLengkap' => (int) ($statsRow->ttd_lengkap ?? 0),
            'totalJabatan' => (int) ($statsRow->total_jabatan ?? 0),
        ];
        
        // Avatar colors for cycling
        $avatarColors = [
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
            'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
            'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
        ];
        
        // Transform paginated pejabats with computed properties (manual loop)
        foreach ($pejabatsPaginated as $index => $pejabat) {
            // Generate initials
            $nama = $pejabat->nama;
            $words = explode(' ', $nama);
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($nama, 0, 2));
            }
            
            // Determine badge class based on jabatan
            $jabatan = strtolower($pejabat->jabatan);
            if (str_contains($jabatan, 'kepala desa')) {
                $badgeClass = 'primary';
            } elseif (str_contains($jabatan, 'sekretaris')) {
                $badgeClass = 'info';
            } elseif (str_contains($jabatan, 'kaur')) {
                $badgeClass = 'warning';
            } elseif (str_contains($jabatan, 'kasi')) {
                $badgeClass = 'success';
            } else {
                $badgeClass = 'secondary';
            }
            
            $pejabat->initials = $initials;
            $pejabat->badge_class = $badgeClass;
            $pejabat->avatar_color = $avatarColors[$index % count($avatarColors)];
        }
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'pejabats' => $pejabatsPaginated->items(),
                'stats' => $stats,
                'pagination' => [
                    'firstItem' => $pejabatsPaginated->firstItem(),
                    'lastItem' => $pejabatsPaginated->lastItem(),
                    'currentPage' => $pejabatsPaginated->currentPage(),
                    'lastPage' => $pejabatsPaginated->lastPage(),
                    'perPage' => $pejabatsPaginated->perPage(),
                    'total' => $pejabatsPaginated->total(),
                ],
            ]);
        }
        
        return view('pejabat.index', [
            'pejabats' => $pejabatsPaginated,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Pejabat::class);
        return view('pejabat.create');
    }

    public function store(StorePejabatRequest $request)
    {
        $this->authorize('create', Pejabat::class);
        $data = $request->validated();
        
        // Handle file upload
        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
            $data['ttd_type'] = $request->input('ttd_type', 'gambar');
        }

        try {
            $pejabat = $this->service->create($data);
            ActivityLogger::log('Pejabat', 'tambah', 'Menambahkan pejabat ' . ($data['nama'] ?? '-'), $pejabat?->id, $data['nama'] ?? null);
            return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil ditambahkan ke sistem!');
        } catch (\Exception $e) {
            Log::error('PejabatController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pejabat. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $pejabat = $this->service->getById($id);
        if (!$pejabat) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('view', $pejabat);
        return view('pejabat.show', compact('pejabat'));
    }

    public function viewTtd($id)
    {
        $pejabat = $this->service->getById($id);
        if (!$pejabat || !$pejabat->ttd_path) {
            abort(404);
        }

        $this->authorize('view', $pejabat);

        // Use 'public' disk since TTD files are stored in storage/app/public/ttd
        $disk = Storage::disk('public');
        if (!$disk->exists($pejabat->ttd_path)) {
            abort(404);
        }

        // Get absolute path using storage_path
        $absolutePath = storage_path('app/public/' . ltrim($pejabat->ttd_path, '/'));
        if (!is_file($absolutePath)) {
            abort(404);
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function edit($id)
    {
        $p = $this->service->getById($id);
        if (!$p) return redirect()->route('pejabat.index')->withErrors('Pejabat tidak ditemukan');
        $this->authorize('update', $p);
        return view('pejabat.edit', ['pejabat' => $p]);
    }

    public function update(UpdatePejabatRequest $request, $id)
    {
        $data = $request->validated();

        // Status aktif/nonaktif pejabat hanya boleh diubah oleh super admin.
        if (!Gate::allows('create', Pejabat::class)) {
            unset($data['is_aktif']);
        }

        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
            $data['ttd_type'] = $request->input('ttd_type', 'gambar');
        }

        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('update', $existing);

        try {
            $this->service->update($id, $data);
            ActivityLogger::log('Pejabat', 'ubah', 'Mengubah data pejabat ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('PejabatController::update error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pejabat. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return redirect()->route('pejabat.index')->with('error', 'Pejabat tidak ditemukan.');
        $this->authorize('delete', $existing);

        try {
            ActivityLogger::log('Pejabat', 'hapus', 'Menghapus pejabat ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            $result = $this->service->delete($id);
            $msg = 'Data pejabat berhasil dihapus dari sistem!';
            if (is_array($result) && ($result['unlinked_users'] ?? 0) > 0) {
                $msg .= ' ' . $result['unlinked_users'] . ' user telah dilepas dari pejabat ini.';
            }
            return redirect()->route('pejabat.index')->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('PejabatController::destroy error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus pejabat. Silakan coba lagi.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        // Authorize sekali di awal — policy delete Pejabat hanya untuk super_admin.
        // Tidak perlu per-item karena tidak ada ownership (bukan "hapus milik sendiri").
        $this->authorize('create', Pejabat::class);

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
        try {
            foreach ($ids as $id) {
                $pejabat = $this->service->getById($id);
                if (!$pejabat) continue;
                ActivityLogger::log('Pejabat', 'hapus', 'Menghapus pejabat ' . ($pejabat->nama ?? '-'), (int) $id, $pejabat->nama ?? null);
                $this->service->delete($id);
                $deleted++;

            }
            $msg = $deleted . ' pejabat berhasil dihapus.';
            if ($totalRequested > 100) {
                $msg .= ' ' . ($totalRequested - 100) . ' data tersisa, pilih ulang untuk menghapus selanjutnya.';
            }
            return redirect()->route('pejabat.index')->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('PejabatController::bulkDestroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

