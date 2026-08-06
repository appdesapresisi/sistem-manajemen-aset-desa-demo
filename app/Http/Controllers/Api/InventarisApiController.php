<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\InventarisValue;
use App\Models\JenisInventarisData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreInventarisRequest;
use App\Http\Requests\UpdateInventarisRequest;

class InventarisApiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Inventaris::class);

        $query = Inventaris::with([
            'jenis:id,nama',
            'values.field',
        ]);

        if ($request->has('jenis_inventaris_id')) {
            $query->where('jenis_inventaris_id', $request->jenis_inventaris_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('nup', 'like', "%{$search}%");
            });
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $inventaris = $query->latest()->paginate($perPage);
        $items = collect($inventaris->items())
            ->map(fn ($item) => $this->serializeInventaris($item))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $inventaris->currentPage(),
                'per_page' => $inventaris->perPage(),
                'total' => $inventaris->total(),
                'last_page' => $inventaris->lastPage(),
            ],
        ]);
    }

    public function store(StoreInventarisRequest $request)
    {
        $this->authorize('create', Inventaris::class);
        $data = $request->validated();

        try {
            DB::beginTransaction();
            
            $inventaris = Inventaris::create([
                'jenis_inventaris_id' => $data['jenis_inventaris_id'],
                'jenis_barang' => $data['jenis_barang'] ?? null,
                'nama_barang' => $data['nama_barang'] ?? null,
                'kode_barang' => $data['kode_barang'] ?? null,
                'nup' => $data['nup'] ?? null,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'kondisi' => $data['kondisi'] ?? null,
                // created_by di-set otomatis oleh Inventaris::booted()
            ]);
            // Set status secara eksplisit (approval workflow removed)
            $inventaris->status = 'draft';
            $inventaris->save();

            foreach ($this->normalizeDynamicInput($request, (int) $inventaris->jenis_inventaris_id) as $fieldId => $value) {
                InventarisValue::create([
                    'inventaris_id' => $inventaris->id,
                    'jenis_inventaris_data_id' => $fieldId,
                    'value' => $value,
                ]);
            }

            // DISABLED: approval records are not created when adding via API
            
            DB::commit();

            $inventaris->load(['jenis:id,nama', 'values.field']);

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil ditambahkan',
                'data' => $this->serializeInventaris($inventaris),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan inventaris.'
            ], 500);
        }
    }

    public function show($id)
    {
        $inventaris = Inventaris::with([
            'jenis:id,nama',
            'jenis.fields',
            'values.field',
        ])->findOrFail($id);

        $this->authorize('view', $inventaris);

        return response()->json([
            'success' => true,
            'data' => $this->serializeInventaris($inventaris),
        ]);
    }

    public function update(UpdateInventarisRequest $request, $id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $this->authorize('update', $inventaris);
        $data = $request->validated();

        try {
            DB::beginTransaction();
            
            $inventaris->update([
                'jenis_inventaris_id' => $data['jenis_inventaris_id'],
                'jenis_barang' => $data['jenis_barang'] ?? null,
                'nama_barang' => $data['nama_barang'] ?? null,
                'kode_barang' => $data['kode_barang'] ?? null,
                'nup' => $data['nup'] ?? null,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'kondisi' => $data['kondisi'] ?? null,
                // updated_by di-set otomatis oleh Inventaris::booted()
            ]);

            foreach ($this->normalizeDynamicInput($request, (int) $inventaris->jenis_inventaris_id) as $fieldId => $value) {
                InventarisValue::updateOrCreate(
                    [
                        'inventaris_id' => $inventaris->id,
                        'jenis_inventaris_data_id' => $fieldId,
                    ],
                    ['value' => $value]
                );
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil diupdate',
                'data' => $this->serializeInventaris($inventaris->fresh(['jenis:id,nama', 'values.field'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate inventaris.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $this->authorize('delete', $inventaris);

        try {
            DB::beginTransaction();
            
            InventarisValue::where('inventaris_id', $id)->delete();
            $inventaris->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus inventaris.'
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        // DISABLED: Approval endpoint has been removed from the workflow.
        // CRUD operations no longer require approval; items are handled directly.
        return response()->json([
            'success' => false,
            'message' => 'Approval endpoint disabled.'
        ], 405);
    }

    protected function normalizeDynamicInput(Request $request, int $jenisId): array
    {
        $dynamic = $request->input('dynamic', $request->input('fields', $request->input('nilai_dinamis', [])));
        if (!is_array($dynamic) || empty($dynamic)) {
            return [];
        }

        $numericIds = [];
        $nameKeys   = [];

        foreach ($dynamic as $key => $value) {
            if (is_numeric($key)) {
                $numericIds[] = (int) $key;
            } else {
                $nameKeys[] = (string) $key;
            }
        }

        // Ambil semua field ID yang valid untuk jenis ini (satu query mencakup keduanya)
        $validFieldsById   = [];
        $validFieldsByName = [];

        if (!empty($numericIds) || !empty($nameKeys)) {
            $query = JenisInventarisData::where('jenis_inventaris_id', $jenisId);

            if (!empty($numericIds) && !empty($nameKeys)) {
                $query->where(function ($q) use ($numericIds, $nameKeys) {
                    $q->whereIn('id', $numericIds)
                      ->orWhereIn('nama_field', $nameKeys);
                });
            } elseif (!empty($numericIds)) {
                $query->whereIn('id', $numericIds);
            } else {
                $query->whereIn('nama_field', $nameKeys);
            }

            $fields = $query->get();
            $validFieldsById   = $fields->keyBy('id');
            $validFieldsByName = $fields->keyBy('nama_field');
        }

        $normalized = [];

        // Numeric keys: hanya izinkan ID yang benar-benar milik jenis ini
        foreach ($numericIds as $id) {
            if (isset($validFieldsById[$id])) {
                $normalized[$id] = $dynamic[$id];
            }
        }

        // Name keys: resolve ke ID, sudah difilter by jenis
        foreach ($nameKeys as $nameKey) {
            if (isset($validFieldsByName[$nameKey])) {
                $normalized[(int) $validFieldsByName[$nameKey]->id] = $dynamic[$nameKey];
            }
        }

        return $normalized;
    }

    protected function serializeInventaris(Inventaris $inventaris): array
    {
        return [
            'id' => $inventaris->id,
            'jenis_inventaris_id' => $inventaris->jenis_inventaris_id,
            'jenis' => $inventaris->jenis ? [
                'id' => $inventaris->jenis->id,
                'nama' => $inventaris->jenis->nama,
            ] : null,
            'kode_barang' => $inventaris->kode_barang,
            'nama_barang' => $inventaris->nama_barang,
            // Backward-compatible aliases
            'kode_inventaris' => $inventaris->kode_barang,
            'nama_inventaris' => $inventaris->nama_barang,
            'nup' => $inventaris->nup,
            'tahun_perolehan' => $inventaris->tahun_perolehan,
            'nilai_perolehan' => $inventaris->nilai_perolehan,
            'keterangan' => $inventaris->keterangan,
            'kondisi' => $inventaris->kondisi,
            'kondisi_label' => $inventaris->kondisi_label,
            'status' => $inventaris->status,
            'created_by' => $inventaris->created_by,
            'updated_by' => $inventaris->updated_by,
            'dynamic_values' => $inventaris->values->map(function ($v) {
                return [
                    'field_id' => $v->jenis_inventaris_data_id,
                    'field_name' => $v->field->nama_field ?? null,
                    'label' => $v->field->label_field ?? null,
                    'value' => $v->value,
                ];
            })->values(),
            'created_at' => $inventaris->created_at,
            'updated_at' => $inventaris->updated_at,
        ];
    }
}
