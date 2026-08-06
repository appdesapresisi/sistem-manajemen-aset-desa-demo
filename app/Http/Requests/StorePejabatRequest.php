<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePejabatRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\Pejabat::class);
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
            'ttd_type'           => 'sometimes|in:gambar,elektronik',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama pejabat wajib diisi',
            'jabatan.required' => 'Jabatan pejabat wajib diisi',
            'ttd_path.mimes' => 'File tanda tangan harus berformat PNG dan JPG.',
            'ttd_path.max' => 'Ukuran file tanda tangan maksimal 5MB',
        ];
    }
}