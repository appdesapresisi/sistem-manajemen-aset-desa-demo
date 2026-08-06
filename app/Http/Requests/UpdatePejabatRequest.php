<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Pejabat;

class UpdatePejabatRequest extends FormRequest
{
    public function authorize()
    {
        $pejabatId = $this->route('pejabat') ?? $this->route('id');
        $pejabat = $pejabatId ? Pejabat::find($pejabatId) : null;

        return $pejabat ? $this->user()->can('update', $pejabat) : false;
    }

    public function rules()
    {
        return [
            'nama'               => 'required|string|max:255',
            'jabatan'            => 'required|string|max:255',
            'tugas'              => 'nullable|string',
            'alamat'             => 'nullable|string|max:500',
            'is_aktif'           => 'sometimes|boolean',
            'is_petugas_barang'  => 'sometimes|boolean',
            'ttd_path'           => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'ttd_type'           => 'sometimes|nullable|in:gambar,elektronik',
        ];
    }
}
