<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\InventarisFoto;
use App\Models\InventarisValue;
use App\Models\JenisInventarisData;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'laporanAsetValue',
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
                'satuan' => $data['satuan'] ?? null,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'asal_usul' => $data['asal_usul'] ?? 'apbdes',
                'bukti_kepemilikan' => $data['bukti_kepemilikan'] ?? null,
                'pengguna' => $data['pengguna'] ?? null,
                'ruangan_id' => $data['ruangan_id'] ?? null,
                'barcode' => !empty($data['barcode']) ? $data['barcode'] : Inventaris::generateBarcode(),
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

            // Simpan KIB values
            $kibData = $request->input('kib', []);
            if (!empty($kibData) && is_array($kibData)) {
                foreach ($kibData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKibValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kib_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Simpan KIR values
            $kirData = $request->input('kir', []);
            if (!empty($kirData) && is_array($kirData)) {
                foreach ($kirData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKirValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kir_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Simpan Laporan Aset values (bulan perolehan)
            $bulanPerolehan = $request->input('bulan_perolehan');
            if ($bulanPerolehan !== null && $bulanPerolehan !== '') {
                \App\Models\InventarisLaporanAsetValue::updateOrCreate(
                    ['inventaris_id' => $inventaris->id],
                    ['bulan_perolehan' => (int)$bulanPerolehan]
                );
            }

            // DISABLED: approval records are not created when adding via API
            
            DB::commit();

            Cache::forget('dashboard_kpi');
            Cache::forget('dashboard_charts');

            ActivityLogger::log('Inventaris', 'tambah',
                'Menambahkan inventaris via API ' . ($inventaris->kode_barang ?? '') . ' - ' . ($inventaris->nama_barang ?? ''),
                $inventaris->id,
                $inventaris->nama_barang ?? $inventaris->kode_barang
            );

            $inventaris->load(['jenis:id,nama', 'values.field', 'laporanAsetValue']);

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil ditambahkan',
                'data' => $this->serializeInventaris($inventaris),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('InventarisApiController::store error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan inventaris: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $inventaris = Inventaris::with([
            'jenis:id,nama',
            'jenis.fields',
            'values.field',
            'laporanAsetValue',
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
                'satuan' => $data['satuan'] ?? $inventaris->satuan,
                'tahun_perolehan' => $data['tahun_perolehan'] ?? null,
                'nilai_perolehan' => $data['nilai_perolehan'] ?? null,
                'asal_usul' => $data['asal_usul'] ?? $inventaris->asal_usul,
                'bukti_kepemilikan' => array_key_exists('bukti_kepemilikan', $data) ? $data['bukti_kepemilikan'] : $inventaris->bukti_kepemilikan,
                'pengguna' => array_key_exists('pengguna', $data) ? $data['pengguna'] : $inventaris->pengguna,
                'ruangan_id' => array_key_exists('ruangan_id', $data) ? $data['ruangan_id'] : $inventaris->ruangan_id,
                'barcode' => !empty($data['barcode']) ? $data['barcode'] : ($inventaris->barcode ?: Inventaris::generateBarcode()),
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

            // Update KIB values
            $kibData = $request->input('kib', []);
            if (!empty($kibData) && is_array($kibData)) {
                foreach ($kibData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKibValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kib_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Update KIR values
            $kirData = $request->input('kir', []);
            if (!empty($kirData) && is_array($kirData)) {
                foreach ($kirData as $key => $value) {
                    if ($value === null || $value === '') continue;
                    \App\Models\InventarisKirValue::updateOrCreate(
                        ['inventaris_id' => $inventaris->id, 'kir_field_key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Update Laporan Aset values (bulan perolehan)
            $bulanPerolehan = $request->input('bulan_perolehan');
            if ($bulanPerolehan !== null && $bulanPerolehan !== '') {
                \App\Models\InventarisLaporanAsetValue::updateOrCreate(
                    ['inventaris_id' => $inventaris->id],
                    ['bulan_perolehan' => (int)$bulanPerolehan]
                );
            }
            
            DB::commit();

            Cache::forget('dashboard_kpi');
            Cache::forget('dashboard_charts');

            ActivityLogger::log('Inventaris', 'ubah',
                'Mengubah inventaris via API ' . ($inventaris->kode_barang ?? '') . ' - ' . ($inventaris->nama_barang ?? ''),
                $inventaris->id,
                $inventaris->nama_barang ?? $inventaris->kode_barang
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil diupdate',
                'data' => $this->serializeInventaris($inventaris->fresh(['jenis:id,nama', 'values.field', 'laporanAsetValue'])),
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
        $inventaris = Inventaris::with('fotos')->findOrFail($id);
        $this->authorize('delete', $inventaris);

        $kodeBarang = $inventaris->kode_barang;
        $namaBarang = $inventaris->nama_barang;

        try {
            DB::beginTransaction();
            
            InventarisValue::where('inventaris_id', $id)->delete();
            \App\Models\InventarisKibValue::where('inventaris_id', $id)->delete();
            \App\Models\InventarisKirValue::where('inventaris_id', $id)->delete();
            \App\Models\InventarisLaporanAsetValue::where('inventaris_id', $id)->delete();
            \App\Models\ExportLog::where('inventaris_id', $id)->update(['inventaris_id' => null]);

            foreach ($inventaris->fotos as $foto) {
                Storage::disk('public')->delete($foto->path);
            }
            InventarisFoto::where('inventaris_id', $id)->delete();

            $barcodePath = public_path('barcodes/barcode_' . $id . '.png');
            if (file_exists($barcodePath)) {
                @unlink($barcodePath);
            }

            $inventaris->delete();
            
            DB::commit();

            Cache::forget('dashboard_kpi');
            Cache::forget('dashboard_charts');

            ActivityLogger::log('Inventaris', 'hapus',
                "Menghapus inventaris via API {$kodeBarang} - {$namaBarang} (Salah Input)",
                $id, $namaBarang
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventaris berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus inventaris: ' . $e->getMessage()
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
            'satuan' => $inventaris->satuan,
            'asal_usul' => $inventaris->asal_usul,
            'bukti_kepemilikan' => $inventaris->bukti_kepemilikan,
            'pengguna' => $inventaris->pengguna,
            'ruangan_id' => $inventaris->ruangan_id,
            'barcode' => $inventaris->barcode,
            'keterangan' => $inventaris->keterangan,
            'kondisi' => $inventaris->kondisi,
            'kondisi_label' => $inventaris->kondisi_label,
            'status' => $inventaris->status,
            'bulan_perolehan' => $inventaris->laporanAsetValue?->bulan_perolehan,
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
