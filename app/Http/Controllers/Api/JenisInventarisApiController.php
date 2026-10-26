<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\JenisInventarisPejabat;
use App\Http\Requests\StoreJenisInventarisRequest;
use App\Http\Requests\UpdateJenisInventarisRequest;
use App\Services\JenisInventarisService;
use App\Services\ActivityLogger;

class JenisInventarisApiController extends Controller
{
    protected $service;

    public function __construct(JenisInventarisService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('viewAny', JenisInventaris::class);
        $list = $this->service->getAll();
        return response()->json($list);
    }

    public function show($id)
    {
        $jenis = $this->service->getById($id);
        if (!$jenis) {
            return response()->json(['message' => 'JenisInventaris tidak ditemukan'], 404);
        }
        $this->authorize('view', $jenis);
        return response()->json($jenis);
    }

    public function store(StoreJenisInventarisRequest $request)
    {
        $this->authorize('create', JenisInventaris::class);
        try {
            $data = $request->validated();
            $jenis = $this->service->create($data);
            ActivityLogger::log('JenisInventaris', 'tambah', 'Menambahkan jenis inventaris ' . ($jenis->nama ?? '-'), $jenis?->id, $jenis->nama ?? null);
            return response()->json($jenis, 201);
        } catch (\Exception $e) {
            Log::error('API store error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error ketika membuat jenis inventaris'], 500);
        }
    }

    public function update(UpdateJenisInventarisRequest $request, $id)
    {
        try {
            $existing = $this->service->getById($id);
            if (!$existing) return response()->json(['message' => 'JenisInventaris tidak ditemukan'], 404);
            $this->authorize('update', $existing);
            $jenis = $this->service->update($id, $request->validated());
            if (!$jenis) return response()->json(['message' => 'JenisInventaris tidak ditemukan'], 404);
            ActivityLogger::log('JenisInventaris', 'ubah', 'Mengubah jenis inventaris ' . ($jenis->nama ?? '-'), (int) $id, $jenis->nama ?? null);
            return response()->json($jenis);
        } catch (\Exception $e) {
            Log::error('API update error', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error ketika mengupdate jenis inventaris'], 500);
        }
    }

    public function destroy($id)
    {
        $existing = $this->service->getById($id);
        if (!$existing) return response()->json(['message' => 'JenisInventaris tidak ditemukan'], 404);
        $this->authorize('delete', $existing);

        if ($existing->isVirtual() || $existing->isTidakDitemukan()) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis inventaris sistem ini dilindungi dan tidak dapat dihapus.'
            ], 422);
        }

        try {
            $deleted = $this->service->delete($id);
            if (!$deleted) return response()->json(['message' => 'JenisInventaris tidak ditemukan'], 404);
            ActivityLogger::log('JenisInventaris', 'hapus', 'Menghapus jenis inventaris ' . ($existing->nama ?? '-'), (int) $id, $existing->nama ?? null);
            return response()->json(['success' => true, 'message' => 'JenisInventaris dihapus']);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
