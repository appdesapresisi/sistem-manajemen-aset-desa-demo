<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pejabat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PejabatService;
use App\Http\Requests\StorePejabatRequest;
use App\Http\Requests\UpdatePejabatRequest;

class PejabatApiController extends Controller
{
    protected $service;

    public function __construct(PejabatService $service)
    {
        $this->service = $service;
    }

    /**
     * Return list of active pejabat for selects
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Pejabat::class);

        // Log::debug('API: fetching pejabats list', ['user' => auth()->id()]);

        $query = Pejabat::query()
            ->where('is_aktif', 1)
            ->orderBy('nama');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $pejabats = $query->get()->map(function($p){
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'tugas' => $p->tugas,
                'ttd_path' => $p->ttd_path,
            ];
        });

        return response()->json(['data' => $pejabats]);
    }

    public function store(StorePejabatRequest $request)
    {
        $this->authorize('create', Pejabat::class);
        $data = $request->validated();
        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
        }
        try {
            $pejabat = $this->service->create($data);
            return response()->json($pejabat, 201);
        } catch (\Exception $e) {
            Log::error('PejabatApiController::store error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error saat membuat pejabat'], 500);
        }
    }

    public function show($id)
    {
        $p = $this->service->getById($id);
        if (!$p) return response()->json(['message' => 'Pejabat tidak ditemukan'], 404);
        $this->authorize('view', $p);
        return response()->json($p);
    }

    public function update(UpdatePejabatRequest $request, $id)
    {
        $data = $request->validated();
        if ($request->hasFile('ttd_path')) {
            $data['ttd_path'] = $request->file('ttd_path');
        }
        try {
            $existing = $this->service->getById($id);
            if (!$existing) return response()->json(['message' => 'Pejabat tidak ditemukan'], 404);
            $this->authorize('update', $existing);
            $pejabat = $this->service->update($id, $data);
            if (!$pejabat) return response()->json(['message' => 'Pejabat tidak ditemukan'], 404);
            return response()->json($pejabat);
        } catch (\Exception $e) {
            Log::error('PejabatApiController::update error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error saat update pejabat'], 500);
        }
    }

    public function destroy($id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return response()->json(['message' => 'Pejabat tidak ditemukan'], 404);
        $this->authorize('delete', $existing);
        $deleted = $this->service->delete($id);
        if (!$deleted) return response()->json(['message' => 'Pejabat tidak ditemukan'], 404);
        return response()->json(['message' => 'Pejabat dihapus']);
    }
}

