<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisInventarisRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\JenisInventaris::class);
    }

    public function rules()
    {
        return [
            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/',
                'unique:jenis_inventaris,nama',
                function ($attribute, $value, $fail) {
                    // Prevent creating duplicate "Inventaris yang tidak ditemukan" template
                    if (trim($value) === \App\Models\JenisInventaris::NAMA_TIDAK_DITEMUKAN) {
                        $fail('Nama jenis inventaris ini sudah ada sebagai jenis template sistem.');
                    }
                },
            ],
            'deskripsi' => 'nullable|string',
            // Icon mode
            'icon_mode'    => 'sometimes|in:auto,manual',
            'custom_icon'  => 'nullable|string|max:50',
            'custom_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            // Requires ruangan
            'requires_ruangan' => 'sometimes|in:0,1',
            // Fixed fields dengan order number
            'fixed_fields' => 'sometimes|array',
            'fixed_fields.*' => 'sometimes|integer|min:1',
            'fixed_fields_required' => 'sometimes|array',
            'fixed_fields_required.*' => 'sometimes|in:0,1',
            // Custom fields
            'fields' => 'sometimes|array',
            'fields.*.label_field' => 'required_with:fields|string|max:150',
            'fields.*.tipe' => 'required_with:fields|string|in:text,number,decimal_m2,decimal_km,decimal_m,year,month,date,dropdown',
            'fields.*.dropdown_id' => 'nullable|integer|exists:dropdowns,id',
            'fields.*.is_required' => 'sometimes|in:0,1',
            'fields.*.include_in_export' => 'sometimes|in:0,1',
            'fields.*.order' => 'sometimes|integer',
            // Fixed fields export
            'fixed_fields_export' => 'sometimes|array',
            'fixed_fields_export.*' => 'sometimes|in:0,1',
            'default_pejabats' => 'sometimes|array',
            'default_pejabats.*' => 'integer|exists:pejabats,id',
            'needs_dropdown' => 'sometimes|in:0,1',
            'selected_dropdowns' => 'sometimes|array',
            'selected_dropdowns.*.enabled' => 'sometimes|in:0,1',
            'selected_dropdowns.*.required' => 'sometimes|in:0,1',
            'selected_dropdowns.*.include_in_export' => 'sometimes|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama jenis inventaris wajib diisi.',
            'nama.min'      => 'Nama jenis inventaris minimal 3 karakter.',
            'nama.regex'    => 'Nama jenis inventaris harus memuat huruf dan tidak boleh hanya berisi angka/simbol.',
            'nama.unique'   => 'Nama jenis inventaris sudah terdaftar, silakan gunakan nama lain.',
        ];
    }
}