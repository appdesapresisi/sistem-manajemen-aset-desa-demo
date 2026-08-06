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
        $sortBy = $request->get('sort', 'nama_asc');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } elseif ($sortBy === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        // Paginate items per page
        $bidangsPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
        $topBidang = Bidang::withCount('users')->orderByDesc('users_count')->first();
        
        // Get total counts
        $stats = [
            'totalBidangs'       => Bidang::count(),
            'totalUsers'         => \App\Models\User::whereNotNull('bidang_id')->count(),
            'bidangWithUsers'    => Bidang::has('users')->count(),
            'bidangWithoutUsers' => Bidang::doesntHave('users')->count(),
            'topBidangNama'      => $topBidang->nama ?? '-',
            'topBidangCount'     => (int) ($topBidang->users_count ?? 0),
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

        ActivityLogger::log('Bidang', 'hapus', 'Menghapus bidang ' . $bidang->nama, $bidang->id, $bidang->nama);
        $bidang->delete();
        
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
                ActivityLogger::log('Bidang', 'hapus', 'Menghapus bidang ' . $bidang->nama, $bidang->id, $bidang->nama);
                $bidang->delete();
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
}
