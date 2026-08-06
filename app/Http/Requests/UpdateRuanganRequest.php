<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $ruanganId = $this->route('ruangan') instanceof \App\Models\Ruangan
            ? $this->route('ruangan')->id
            : $this->route('ruangan');

        return [
            'nama'                 => ['required', 'string', 'max:150'],
            'kode'                 => ['required', 'integer', 'min:1', "unique:ruangans,kode,{$ruanganId}"],
            'deskripsi'            => ['nullable', 'string'],
            'penanggung_jawab_id'  => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama ruangan wajib diisi.',
            'kode.required' => 'Kode ruangan wajib diisi.',
            'kode.integer'  => 'Kode ruangan harus berupa angka.',
            'kode.min'      => 'Kode ruangan minimal 1.',
            'kode.unique'   => 'Kode ruangan sudah digunakan, pilih nomor lain.',
        ];
    }
}
