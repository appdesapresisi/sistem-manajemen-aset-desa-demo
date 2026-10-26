<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        $ruanganId = $this->route('ruangan') instanceof \App\Models\Ruangan
            ? $this->route('ruangan')->id
            : $this->route('ruangan');

        return [
            'nama'                 => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/',
            ],
            'kode'                 => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9\-_]+$/', "unique:ruangans,kode,{$ruanganId}"],
            'deskripsi'            => ['nullable', 'string', 'regex:/[a-zA-Z]/'],
            'penanggung_jawab_id'  => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama ruangan wajib diisi.',
            'nama.min'      => 'Nama ruangan minimal 3 karakter.',
            'nama.regex'    => 'Nama ruangan harus memuat huruf dan tidak boleh hanya berisi angka/simbol.',
            'kode.required' => 'Kode ruangan wajib diisi.',
            'kode.string'   => 'Kode ruangan harus berupa teks yang valid.',
            'kode.max'      => 'Kode ruangan maksimal 50 karakter.',
            'kode.regex'    => 'Kode ruangan hanya boleh memuat huruf, angka, tanda minus (-), dan garis bawah (_).',
            'kode.unique'   => 'Kode ruangan sudah digunakan, pilih kode lain.',
            'deskripsi.regex' => 'Deskripsi ruangan harus memuat teks huruf.',
        ];
    }
}
