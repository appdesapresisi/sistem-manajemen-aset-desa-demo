<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisInventarisFieldApiController extends Controller
{
    /**
     * Return field definitions for a given jenis_inventaris id
     */
    public function show(Request $request, $id)
    {
        $userId = auth()->id();
        Log::info('API: fetching jenis_inventaris fields', ['jenis_id' => $id, 'user' => $userId]);

        $jenis = JenisInventaris::with(['fields' => function($q){ $q->orderBy('order'); }])->find($id);
        if (!$jenis) {
            Log::warning('API: jenis_inventaris not found', ['jenis_id' => $id, 'user' => $userId]);
            return response()->json(['message' => 'Jenis inventaris not found'], 404);
        }

        $this->authorize('view', $jenis);

        $payload = [
            'id' => $jenis->id,
            'nama' => $jenis->nama,
            'deskripsi' => $jenis->deskripsi,
            'kib_type' => $jenis->kib_type,
            'fields' => $jenis->fields->map(function($f){
                $data = [
                    'id' => $f->id,
                    'nama_field' => $f->nama_field,
                    'label_field' => $f->label_field,
                    'tipe' => $f->tipe,
                    'field_type' => $f->field_type,
                    'fixed_field_name' => $f->fixed_field_name,
                    'is_required' => (bool) $f->is_required,
                    'order' => $f->order,
                    'dropdown_id' => $f->dropdown_id,
                ];
                // Jika tipe dropdown, sertakan items-nya
                if ($f->tipe === 'dropdown' && $f->dropdown_id) {
                    $dropdown = \App\Models\Dropdown::with('items')->find((int)$f->dropdown_id);
                    $data['dropdown_items'] = $dropdown
                        ? $dropdown->items->map(fn($i) => ['id' => $i->id, 'label' => $i->label])->values()->toArray()
                        : [];
                    $data['dropdown_nama'] = $dropdown?->nama ?? '';
                    Log::info('API: dropdown field loaded', ['dropdown_id' => $f->dropdown_id, 'items_count' => count($data['dropdown_items'])]);
                }
                return $data;
            })->values(),
        ];

        return response()->json($payload);
    }
}
