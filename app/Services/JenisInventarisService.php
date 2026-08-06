<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\JenisInventarisPejabat;

class JenisInventarisService
{
    public function getAll()
    {
        return JenisInventaris::with(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat'])->paginate(10);
    }

    public function find($id)
    {
        return JenisInventaris::with(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat'])->find($id);
    }

    public function getById($id)
    {
        return $this->find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $jenis = JenisInventaris::create([
                'nama'             => $data['nama'],
                'deskripsi'        => $data['deskripsi'] ?? null,
                'icon_mode'        => $data['icon_mode'] ?? 'auto',
                'custom_icon'      => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_icon'] ?? null) : null,
                'custom_color'     => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_color'] ?? null) : null,
                'requires_ruangan' => isset($data['requires_ruangan']) ? 1 : 0,
            ]);

            // 1. Insert ENABLED fixed fields dengan order dari user
            $fixedFields = $data['fixed_fields'] ?? [];
            foreach ($fixedFields as $fieldName => $order) {
                // Get label dari constant
                $fieldConfig = JenisInventarisData::AVAILABLE_FIXED_FIELDS[$fieldName] ?? null;
                if ($fieldConfig) {
                    $isRequired = isset($data['fixed_fields_required'][$fieldName])
                        ? (int) $data['fixed_fields_required'][$fieldName]
                        : 0;
                    $includeInExport = isset($data['fixed_fields_export'][$fieldName])
                        ? (int) $data['fixed_fields_export'][$fieldName]
                        : 1; // default: masuk export
                    JenisInventarisData::create([
                        'jenis_inventaris_id' => $jenis->id,
                        'field_type' => 'fixed',
                        'fixed_field_name' => $fieldName,
                        'nama_field' => $fieldName,
                        'label_field' => $fieldConfig['label'],
                        'tipe' => $fieldConfig['type'],
                        'is_required' => $isRequired,
                        'include_in_export' => $includeInExport,
                        'order' => $order,
                    ]);
                }
            }

            // 2. Insert CUSTOM fields (from user input)
            $fields = $data['fields'] ?? [];
            foreach ($fields as $i => $f) {
                // Auto-generate nama_field dari label_field
                $namaField = $this->generateNamaField($f['label_field']);
                
                // Use order from form if provided
                $order = isset($f['order']) ? $f['order'] : 999;
                
                JenisInventarisData::create([
                    'jenis_inventaris_id' => $jenis->id,
                    'field_type' => 'custom',
                    'fixed_field_name' => null,
                    'nama_field' => $namaField,
                    'label_field' => $f['label_field'],
                    'tipe' => $f['tipe'] ?? 'text',
                    'dropdown_id' => ($f['tipe'] ?? 'text') === 'dropdown' ? ($f['dropdown_id'] ?? null) : null,
                    'is_required' => $f['is_required'] ?? 0,
                    'include_in_export' => array_key_exists('include_in_export', $f) ? (int)$f['include_in_export'] : 1,
                    'order' => $order,
                ]);
            }

            // 3. Insert dropdown fields dari selected_dropdowns
            $selectedDropdowns = $data['selected_dropdowns'] ?? [];
            foreach ($selectedDropdowns as $dropdownId => $opts) {
                if (empty($opts['enabled'])) continue;
                $dd = \App\Models\Dropdown::find($dropdownId);
                if (!$dd) continue;
                $namaField = 'dropdown_' . \Illuminate\Support\Str::slug($dd->nama, '_');
                JenisInventarisData::create([
                    'jenis_inventaris_id' => $jenis->id,
                    'field_type'          => 'custom',
                    'fixed_field_name'    => null,
                    'nama_field'          => $namaField,
                    'label_field'         => $dd->nama,
                    'tipe'                => 'dropdown',
                    'dropdown_id'         => $dropdownId,
                    'is_required'         => !empty($opts['required']) ? 1 : 0,
                    'include_in_export'   => isset($opts['include_in_export']) ? 1 : 0,
                    'order'               => isset($opts['order']) ? (int)$opts['order'] : 999,
                ]);
            }

            // 4. Create pejabat defaults
            $pejabatIds = $data['default_pejabats'] ?? [];
            foreach ($pejabatIds as $i => $pid) {
                JenisInventarisPejabat::create([
                    'jenis_inventaris_id' => $jenis->id,
                    'pejabat_id' => $pid,
                    'order' => $i + 1,
                ]);
            }

            DB::commit();
            $jenis->load(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat']);
            return $jenis;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JenisInventarisService::create error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        $jenis = JenisInventaris::find($id);
        if (!$jenis) return null;

        // Jenis template "Inventaris yang tidak ditemukan" (LHI):
        // field pakem terkunci. Hanya update deskripsi, icon, pejabat,
        // urutan field & status wajib. Nama, requires_ruangan, dropdown,
        // dan struktur field TIDAK diubah.
        if ($jenis->isTidakDitemukan()) {
            return $this->updateTidakDitemukan($jenis, $data);
        }

        DB::beginTransaction();
        try {
            $jenis->update([
                'nama'             => $data['nama'],
                'deskripsi'        => $data['deskripsi'] ?? null,
                'icon_mode'        => $data['icon_mode'] ?? $jenis->icon_mode ?? 'auto',
                'custom_icon'      => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_icon'] ?? null) : null,
                'custom_color'     => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_color'] ?? null) : null,
                'requires_ruangan' => isset($data['requires_ruangan']) ? 1 : 0,
            ]);

            // IMPROVED: Don't delete all - sync fields properly
            // Get existing field IDs yang akan dipertahankan
            $keepFieldIds = [];
            
            // 1. Update atau create fixed fields
            $fixedFields = $data['fixed_fields'] ?? [];
            foreach ($fixedFields as $fieldName => $order) {
                $fieldConfig = JenisInventarisData::AVAILABLE_FIXED_FIELDS[$fieldName] ?? null;
                if ($fieldConfig) {
                    $isRequired = isset($data['fixed_fields_required'][$fieldName])
                        ? (int) $data['fixed_fields_required'][$fieldName]
                        : 0;
                    $includeInExport = isset($data['fixed_fields_export'][$fieldName])
                        ? (int) $data['fixed_fields_export'][$fieldName]
                        : 1; // default: masuk export
                    $field = JenisInventarisData::updateOrCreate(
                        [
                            'jenis_inventaris_id' => $jenis->id,
                            'field_type' => 'fixed',
                            'fixed_field_name' => $fieldName,
                        ],
                        [
                            'nama_field' => $fieldName,
                            'label_field' => $fieldConfig['label'],
                            'tipe' => $fieldConfig['type'],
                            'is_required' => $isRequired,
                            'include_in_export' => $includeInExport,
                            'order' => $order,
                        ]
                    );
                    $keepFieldIds[] = $field->id;
                }
            }
            
            // 2. Update atau create custom fields (dari form)
            $fields = $data['fields'] ?? [];
            foreach ($fields as $i => $f) {
                $namaField = $this->generateNamaField($f['label_field']);
                $order = isset($f['order']) ? $f['order'] : 999;
                
                // Jika ada field_id (existing field), update
                if (!empty($f['id'])) {
                    $field = JenisInventarisData::find($f['id']);
                    if ($field && $field->jenis_inventaris_id == $jenis->id) {
                        $field->update([
                            'nama_field' => $namaField,
                            'label_field' => $f['label_field'],
                            'tipe' => $f['tipe'] ?? 'text',
                            'dropdown_id' => ($f['tipe'] ?? 'text') === 'dropdown' ? ($f['dropdown_id'] ?? null) : null,
                            'is_required' => $f['is_required'] ?? 0,
                            'include_in_export' => array_key_exists('include_in_export', $f) ? (int)$f['include_in_export'] : 1,
                            'order' => $order,
                        ]);
                        $keepFieldIds[] = $field->id;
                    }
                } else {
                    // New field - create
                    $field = JenisInventarisData::create([
                        'jenis_inventaris_id' => $jenis->id,
                        'field_type' => 'custom',
                        'fixed_field_name' => null,
                        'nama_field' => $namaField,
                        'label_field' => $f['label_field'],
                        'tipe' => $f['tipe'] ?? 'text',
                        'dropdown_id' => ($f['tipe'] ?? 'text') === 'dropdown' ? ($f['dropdown_id'] ?? null) : null,
                        'is_required' => $f['is_required'] ?? 0,
                        'include_in_export' => array_key_exists('include_in_export', $f) ? (int)$f['include_in_export'] : 1,
                        'order' => $order,
                    ]);
                    $keepFieldIds[] = $field->id;
                }
            }
            
            // 3. Delete custom fields specifically marked for deletion
            // User explicitly unchecked these fields and wants them removed
            $deletedCustomFieldIds = $data['deleted_custom_fields'] ?? [];

            // OPSI A: Sebelum hapus field custom, cek apakah ada field yang nilainya
            // dipakai sebagai "nama tampilan" aset (order terendah / field pertama).
            // Jika iya, copy nilainya ke kolom nama_barang di tabel inventaris agar
            // nama aset tidak hilang setelah field dihapus.
            $allToDeleteIds = array_merge(
                $deletedCustomFieldIds,
                // Tambahkan stale field IDs yang akan dihapus di step 4
                array_key_exists('fields', $data)
                    ? JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                        ->where('field_type', 'custom')
                        ->whereNotIn('id', $keepFieldIds)
                        ->whereNotIn('id', $deletedCustomFieldIds)
                        ->pluck('id')->toArray()
                    : []
            );

            if (!empty($allToDeleteIds)) {
                // Ambil field yang akan dihapus, urutkan by order
                $fieldsToDelete = JenisInventarisData::whereIn('id', $allToDeleteIds)
                    ->where('jenis_inventaris_id', $jenis->id)
                    ->orderBy('order')
                    ->get();

                foreach ($fieldsToDelete as $deletedField) {
                    // Cek apakah field ini adalah "nama tampilan" — yaitu field text
                    // dengan order terendah yang nilainya dipakai sebagai display_name
                    $isNameField = in_array($deletedField->tipe ?? 'text', ['text', 'textarea'])
                        && ($deletedField->is_name_field ?? false);

                    // Fallback: cek apakah ini field text pertama by order di jenis ini
                    // (logika sama dengan getDisplayNameAttribute fallback)
                    if (!$isNameField) {
                        $lowestOrderCustomField = JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                            ->where('field_type', 'custom')
                            ->whereIn('tipe', ['text', 'textarea'])
                            ->orderBy('order')
                            ->first();
                        $isNameField = $lowestOrderCustomField && $lowestOrderCustomField->id === $deletedField->id;
                    }

                    if ($isNameField) {
                        // Copy nilai field ini ke nama_barang untuk semua aset yang belum punya nama_barang
                        $values = \App\Models\InventarisValue::where('jenis_inventaris_data_id', $deletedField->id)
                            ->whereNotNull('value')
                            ->where('value', '!=', '')
                            ->get();

                        foreach ($values as $val) {
                            \App\Models\Inventaris::where('id', $val->inventaris_id)
                                ->whereNull('nama_barang')
                                ->update(['nama_barang' => $val->value]);
                        }
                    }
                }
            }

            if (!empty($deletedCustomFieldIds)) {
                $deleteCount = JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                    ->where('field_type', 'custom')
                    ->whereIn('id', $deletedCustomFieldIds)
                    ->delete();
            }
            
            // 4. Delete custom fields that are not in the form (not selected) AND not in disabled list
            // Safety: Only delete custom fields if form explicitly sent field data
            if (array_key_exists('fields', $data)) {
                $deleteCount = JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                    ->where('field_type', 'custom')
                    ->whereNotIn('id', $keepFieldIds)
                    ->whereNotIn('id', $deletedCustomFieldIds)
                    ->delete();
                /* Log::debug('JenisInventarisService.update: deleted stale fields', [
                    'jenis_id' => $jenis->id,
                    'deleted_count' => $deleteCount,
                ]); */
            }

            // 5. Delete fixed fields that are not in the form (not selected)
            // Safety: Only delete fixed fields if form explicitly sent fixed_fields data
            if (array_key_exists('fixed_fields', $data)) {
                JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                    ->where('field_type', 'fixed')
                    ->whereNotIn('id', $keepFieldIds)
                    ->delete();
            }

            // Update dropdown fields dari selected_dropdowns
            // PENTING: jangan delete+recreate (akan orphan inventaris_values yang refer ke ID lama).
            // Pakai updateOrCreate agar field ID dipertahankan → nilai dropdown di inventaris tetap nyambung.
            $selectedDropdowns = $data['selected_dropdowns'] ?? [];
            $keptDropdownIds = []; // dropdown_id yang masih dipilih
            foreach ($selectedDropdowns as $dropdownId => $opts) {
                if (empty($opts['enabled'])) continue;
                $dd = \App\Models\Dropdown::find($dropdownId);
                if (!$dd) continue;
                $namaField = 'dropdown_' . \Illuminate\Support\Str::slug($dd->nama, '_');
                JenisInventarisData::updateOrCreate(
                    [
                        'jenis_inventaris_id' => $jenis->id,
                        'tipe'                => 'dropdown',
                        'dropdown_id'         => $dropdownId,
                    ],
                    [
                        'field_type'          => 'custom',
                        'fixed_field_name'    => null,
                        'nama_field'          => $namaField,
                        'label_field'         => $dd->nama,
                        'is_required'         => !empty($opts['required']) ? 1 : 0,
                        'include_in_export'   => isset($opts['include_in_export']) ? 1 : 0,
                        'order'               => isset($opts['order']) ? (int)$opts['order'] : 999,
                    ]
                );
                $keptDropdownIds[] = $dropdownId;
            }
            // Hapus HANYA dropdown field yang sudah tidak dipilih lagi (tidak ada di keptDropdownIds)
            $staleDropdowns = JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                ->where('tipe', 'dropdown')
                ->whereNotNull('dropdown_id');
            if (!empty($keptDropdownIds)) {
                $staleDropdowns->whereNotIn('dropdown_id', $keptDropdownIds);
            }
            $staleDropdowns->delete();

            // Update pejabat HANYA jika user explicitly changed it
            // If pejabat_changed flag is present and true, update pejabat
            // If flag is absent, preserve existing pejabat
            if (isset($data['pejabat_changed']) && $data['pejabat_changed']) {
                $pejabatIds = $data['default_pejabats'] ?? [];
                $newPejabatData = [];
                foreach ($pejabatIds as $i => $pid) {
                    $newPejabatData[] = [
                        'jenis_inventaris_id' => $jenis->id,
                        'pejabat_id' => $pid,
                        'order' => $i + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                JenisInventarisPejabat::where('jenis_inventaris_id', $jenis->id)->delete();
                if (!empty($newPejabatData)) {
                    JenisInventarisPejabat::insert($newPejabatData);
                }
            }

            DB::commit();
            $jenis->load(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat']);
            return $jenis;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JenisInventarisService::update error', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update khusus jenis template "Inventaris yang tidak ditemukan" (LHI).
     * Hanya mengubah: deskripsi, icon, pejabat penandatangan, urutan field,
     * dan status wajib field. Nama, requires_ruangan, dropdown, dan struktur
     * field (tambah/hapus) TIDAK disentuh.
     */
    protected function updateTidakDitemukan(JenisInventaris $jenis, array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Update hanya deskripsi & icon (nama & requires_ruangan dipertahankan)
            $jenis->update([
                'deskripsi'    => $data['deskripsi'] ?? $jenis->deskripsi,
                'icon_mode'    => $data['icon_mode'] ?? $jenis->icon_mode ?? 'auto',
                'custom_icon'  => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_icon'] ?? null) : null,
                'custom_color' => ($data['icon_mode'] ?? 'auto') === 'manual' ? ($data['custom_color'] ?? null) : null,
            ]);

            // 2. Update urutan & status wajib field pakem (tanpa tambah/hapus field)
            // Form mengirim fields[] berisi {id, order, is_required} untuk field existing.
            $fields = $data['fields'] ?? [];
            foreach ($fields as $f) {
                if (empty($f['id'])) continue;
                $field = JenisInventarisData::find($f['id']);
                if (!$field || $field->jenis_inventaris_id != $jenis->id) continue;
                $field->update([
                    'order'       => isset($f['order']) ? (int) $f['order'] : ($field->order ?? 999),
                    'is_required' => !empty($f['is_required']) ? 1 : 0,
                ]);
            }

            // Dukung juga format fixed_fields (order) + fixed_fields_required dari form
            $fixedFields = $data['fixed_fields'] ?? [];
            foreach ($fixedFields as $fieldName => $order) {
                $field = JenisInventarisData::where('jenis_inventaris_id', $jenis->id)
                    ->where('fixed_field_name', $fieldName)
                    ->first();
                if (!$field) continue;
                $isRequired = isset($data['fixed_fields_required'][$fieldName])
                    ? (int) $data['fixed_fields_required'][$fieldName]
                    : 0;
                $field->update([
                    'order'       => (int) $order,
                    'is_required' => $isRequired,
                ]);
            }

            // 3. Update pejabat penandatangan (jika diubah)
            if (isset($data['pejabat_changed']) && $data['pejabat_changed']) {
                $pejabatIds = $data['default_pejabats'] ?? [];
                $newPejabatData = [];
                foreach ($pejabatIds as $i => $pid) {
                    $newPejabatData[] = [
                        'jenis_inventaris_id' => $jenis->id,
                        'pejabat_id' => $pid,
                        'order' => $i + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                JenisInventarisPejabat::where('jenis_inventaris_id', $jenis->id)->delete();
                if (!empty($newPejabatData)) {
                    JenisInventarisPejabat::insert($newPejabatData);
                }
            }

            DB::commit();
            $jenis->load(['fields' => function($q){ $q->orderBy('order'); }, 'pejabatDefaults.pejabat']);
            return $jenis;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JenisInventarisService::updateTidakDitemukan error', ['id' => $jenis->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete($id)
    {
        $jenis = JenisInventaris::find($id);
        if (!$jenis) return false;

        // Cek apakah jenis ini masih digunakan oleh inventaris
        $jumlahInventaris = $jenis->inventaris()->count();
        
        if ($jumlahInventaris > 0) {
            throw new \Exception("Tidak dapat menghapus jenis inventaris ini karena masih digunakan oleh {$jumlahInventaris} inventaris. Hapus atau pindahkan inventaris tersebut terlebih dahulu.");
        }

        DB::transaction(function () use ($jenis) {
            JenisInventarisData::where('jenis_inventaris_id', $jenis->id)->delete();
            JenisInventarisPejabat::where('jenis_inventaris_id', $jenis->id)->delete();
            $jenis->delete();
        });

        return true;
    }

    /**
    /**
     * Generate nama_field dari label_field
     * Contoh: "Nama Barang" -> "nama_barang"
     * 
     * @param string $label
     * @return string
     */
    private function generateNamaField($label)
    {
        // Lowercase, replace spasi dengan underscore, remove special chars
        $namaField = strtolower(trim($label));
        $namaField = str_replace(' ', '_', $namaField);
        // Remove non-alphanumeric except underscore
        $namaField = preg_replace('/[^a-z0-9_]/', '', $namaField);
        // Remove multiple underscores
        $namaField = preg_replace('/_+/', '_', $namaField);
        // Remove leading/trailing underscores
        $namaField = trim($namaField, '_');
        
        return $namaField;
    }
}
