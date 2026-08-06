<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Ruangan;
use App\Services\ActivityLogger;
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

        $sortBy = $request->get('sort', 'nama_asc');
        match ($sortBy) {
            'nama_desc' => $query->orderBy('nama', 'desc'),
            'terbaru'   => $query->orderBy('created_at', 'desc'),
            'terlama'   => $query->orderBy('created_at', 'asc'),
            default     => $query->orderBy('nama', 'asc'),
        };

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $ruangans = $query->paginate($perPage)->withQueryString();

        $stats = [
            'totalRuangan'    => Ruangan::count(),
            'totalAset'       => \App\Models\Inventaris::whereNotNull('ruangan_id')->count(),
            'ruanganTerisi'   => Ruangan::has('inventaris')->count(),
            'ruanganKosong'   => Ruangan::doesntHave('inventaris')->count(),
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

    public function create()
    {
        $this->authorize('create', Ruangan::class);
        $userList = \App\Models\User::where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);
        return view('ruangan.create', compact('userList'));
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

        // Paginate aset di ruangan ini, sorted by nama jenis lalu kode barang
        $inventaris = $ruangan->inventaris()
            ->with('jenis')
            ->join('jenis_inventaris', 'inventaris.jenis_inventaris_id', '=', 'jenis_inventaris.id')
            ->orderBy('jenis_inventaris.nama', 'asc')
            ->orderBy('inventaris.kode_barang', 'asc')
            ->select('inventaris.*')
            ->paginate(10)
            ->withQueryString();

        // AJAX request → return JSON untuk partial update tabel
        if ($request->ajax() || $request->wantsJson()) {
            $items = $inventaris->map(function ($aset) {
                return [
                    'id'           => $aset->id,
                    'display_name' => $aset->display_name ?? '-',
                    'jenis_nama'   => $aset->jenis->nama ?? '-',
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
        $userList = \App\Models\User::where('is_aktif', 1)->orderBy('nama')->with(['role','bidang','dinas'])->get(['id','nama','role_id','bidang_id','dinas_id']);
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
            ActivityLogger::log('Ruangan', 'hapus', 'Menghapus ruangan ' . $ruangan->nama, $ruangan->id, $ruangan->nama);
            $ruangan->delete();
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
            ActivityLogger::log('Ruangan', 'hapus', 'Menghapus ruangan ' . $ruangan->nama, $ruangan->id, $ruangan->nama);
            $ruangan->delete();
            $deleted++;
        }

        $msg = $deleted . ' ruangan berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Tidak dapat dihapus: ' . implode(', ', $skipped) . '.';
        }

        return redirect()->route('ruangan.index')->with($deleted > 0 ? 'success' : 'error', $msg);
    }
}
