<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDropdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $dropdownId = $this->route('dropdown') instanceof \App\Models\Dropdown
            ? $this->route('dropdown')->id
            : $this->route('dropdown');

        return [
            'nama'          => ['required', 'string', 'max:150', "unique:dropdowns,nama,{$dropdownId}"],
            'deskripsi'     => ['nullable', 'string'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*.label' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'         => 'Nama dropdown wajib diisi.',
            'nama.unique'           => 'Nama dropdown sudah digunakan.',
            'items.required'        => 'Minimal satu pilihan harus ditambahkan.',
            'items.min'             => 'Minimal satu pilihan harus ditambahkan.',
            'items.*.label.required'=> 'Label pilihan tidak boleh kosong.',
        ];
    }
}
