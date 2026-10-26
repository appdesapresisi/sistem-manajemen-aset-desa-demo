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
            'nama'               => [
                'required',
                'string',
                'min:3',
                'max:255',
                "regex:/^[a-zA-Z\s\.,'\-]+$/"
            ],
            'jabatan'            => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/'
            ],
            'tugas'              => [
                'nullable',
                'string',
                'regex:/[a-zA-Z]/'
            ],
            'alamat'             => 'nullable|string|max:500',
            'is_aktif'           => 'sometimes|boolean',
            'is_petugas_barang'  => 'sometimes|boolean',
            'ttd_path'           => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
            'ttd_type'           => 'sometimes|in:gambar,elektronik',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama pejabat wajib diisi.',
            'nama.min' => 'Nama pejabat minimal 3 karakter.',
            'nama.regex' => 'Nama pejabat hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh mengandung angka).',
            'jabatan.required' => 'Jabatan pejabat wajib diisi.',
            'jabatan.min' => 'Jabatan pejabat minimal 3 karakter.',
            'jabatan.regex' => 'Jabatan pejabat harus memuat huruf dan tidak boleh hanya berisi angka/simbol.',
            'tugas.regex' => 'Tugas / kewenangan harus memuat teks huruf yang jelas.',
            'ttd_path.mimes' => 'File tanda tangan harus berupa gambar (PNG, JPG, JPEG, WEBP).',
            'ttd_path.max' => 'Ukuran file tanda tangan maksimal 5MB.',
        ];
    }
}