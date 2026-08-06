<?php

namespace App\Http\Controllers\Dropdown;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDropdownRequest;
use App\Http\Requests\UpdateDropdownRequest;
use App\Models\Dropdown;
use App\Models\DropdownItem;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DropdownController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Dropdown::class);

        $query = Dropdown::withCount('items');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'digunakan') {
                $query->has('jenisInventarisData');
            } elseif ($request->status === 'tidak_digunakan') {
                $query->doesntHave('jenisInventarisData');
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

        $dropdowns = $query->paginate($perPage)->withQueryString();

        $topDropdown = \App\Models\Dropdown::withCount('items')->orderByDesc('items_count')->first();

        $stats = [
            'totalDropdowns'   => \App\Models\Dropdown::count(),
            'totalItems'       => \App\Models\DropdownItem::count(),
            'usedInJenis'      => \App\Models\Dropdown::has('jenisInventarisData')->count(),
            'topDropdownNama'  => $topDropdown->nama ?? '-',
            'topDropdownCount' => (int) ($topDropdown->items_count ?? 0),
        ];

        if ($request->ajax()) {
            return response()->json([
                'dropdowns'  => $dropdowns->items(),
                'stats'      => $stats,
                'pagination' => [
                    'firstItem'   => $dropdowns->firstItem(),
                    'lastItem'    => $dropdowns->lastItem(),
                    'currentPage' => $dropdowns->currentPage(),
                    'lastPage'    => $dropdowns->lastPage(),
                    'perPage'     => $dropdowns->perPage(),
                    'total'       => $dropdowns->total(),
                ],
            ]);
        }

        return view('dropdown.index', compact('dropdowns', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', Dropdown::class);
        return view('dropdown.create');
    }

    public function store(StoreDropdownRequest $request)
    {
        $this->authorize('create', Dropdown::class);

        DB::beginTransaction();
        try {
            $dropdown = Dropdown::create([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            foreach ($request->items as $i => $item) {
                if (empty(trim($item['label']))) continue;
                DropdownItem::create([
                    'dropdown_id' => $dropdown->id,
                    'label'       => trim($item['label']),
                    'order'       => $i + 1,
                ]);
            }

            DB::commit();
            ActivityLogger::log('Dropdown', 'tambah', 'Menambahkan dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil ditambahkan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DropdownController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dropdown.');
        }
    }

    public function show(Dropdown $dropdown)
    {
        $this->authorize('view', $dropdown);
        $dropdown->load('items');
        return view('dropdown.show', compact('dropdown'));
    }

    public function edit(Dropdown $dropdown)
    {
        $this->authorize('update', $dropdown);
        $dropdown->load('items');
        return view('dropdown.edit', compact('dropdown'));
    }

    public function update(UpdateDropdownRequest $request, Dropdown $dropdown)
    {
        $this->authorize('update', $dropdown);

        DB::beginTransaction();
        try {
            $dropdown->update([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            // Hapus semua item lama, insert ulang
            DropdownItem::where('dropdown_id', $dropdown->id)->delete();
            foreach ($request->items as $i => $item) {
                if (empty(trim($item['label']))) continue;
                DropdownItem::create([
                    'dropdown_id' => $dropdown->id,
                    'label'       => trim($item['label']),
                    'order'       => $i + 1,
                ]);
            }

            DB::commit();
            ActivityLogger::log('Dropdown', 'ubah', 'Mengubah dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DropdownController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dropdown.');
        }
    }

    public function bulkDestroy(\Illuminate\Http\Request $request)
    {
        $this->authorize('delete', Dropdown::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids     = array_slice((array) $request->input('ids', []), 0, 100);
        $deleted = 0;
        $skipped = [];

        foreach ($ids as $id) {
            $dropdown = Dropdown::find($id);
            if (!$dropdown) continue;
            $usedCount = \App\Models\JenisInventarisData::where('dropdown_id', $id)->count();
            if ($usedCount > 0) {
                $skipped[] = $dropdown->nama . ' (masih digunakan)';
                continue;
            }
            ActivityLogger::log('Dropdown', 'hapus', 'Menghapus dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            $dropdown->delete();
            $deleted++;
        }

        $msg = $deleted . ' dropdown berhasil dihapus.';
        if (!empty($skipped)) {
            $msg .= ' Tidak dapat dihapus: ' . implode(', ', $skipped) . '.';
        }

        return redirect()->route('dropdown.index')->with($deleted > 0 ? 'success' : 'error', $msg);
    }

    public function destroy(Dropdown $dropdown)
    {
        $this->authorize('delete', $dropdown);

        // Cek apakah masih dipakai di jenis inventaris
        $usedCount = \App\Models\JenisInventarisData::where('dropdown_id', $dropdown->id)->count();
        if ($usedCount > 0) {
            return redirect()->back()->with('error',
                "Tidak dapat menghapus dropdown '{$dropdown->nama}' karena masih digunakan oleh {$usedCount} field jenis inventaris.");
        }

        try {
            ActivityLogger::log('Dropdown', 'hapus', 'Menghapus dropdown ' . $dropdown->nama, $dropdown->id, $dropdown->nama);
            $dropdown->delete();
            return redirect()->route('dropdown.index')->with('success', 'Dropdown berhasil dihapus!');
        } catch (\Throwable $e) {
            Log::error('DropdownController::destroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus dropdown.');
        }
    }
}
