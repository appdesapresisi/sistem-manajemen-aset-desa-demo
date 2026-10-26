<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDropdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        $dropdownId = $this->route('dropdown') instanceof \App\Models\Dropdown
            ? $this->route('dropdown')->id
            : $this->route('dropdown');

        return [
            'nama'          => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/',
                "unique:dropdowns,nama,{$dropdownId}"
            ],
            'deskripsi'     => ['nullable', 'string', 'regex:/[a-zA-Z]/'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*.label' => [
                'required',
                'string',
                'max:150',
                'regex:/^(?=.*[a-zA-Z0-9])[a-zA-Z0-9\s\/\.,\-\(\)]+$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'         => 'Nama dropdown wajib diisi.',
            'nama.min'              => 'Nama dropdown minimal 3 karakter.',
            'nama.regex'            => 'Nama dropdown harus memuat huruf dan tidak boleh hanya berisi angka/simbol.',
            'nama.unique'           => 'Nama dropdown sudah digunakan.',
            'items.required'        => 'Minimal satu pilihan harus ditambahkan.',
            'items.min'             => 'Minimal satu pilihan harus ditambahkan.',
            'items.*.label.required'=> 'Label pilihan tidak boleh kosong.',
            'items.*.label.regex'   => 'Label pilihan harus memuat teks yang valid.',
        ];
    }
}
