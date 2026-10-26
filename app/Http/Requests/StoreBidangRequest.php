<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBidangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperDeveloper() || auth()->user()->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/',
                'unique:bidangs,nama',
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'regex:/[a-zA-Z]/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama bidang wajib diisi.',
            'nama.min' => 'Nama bidang minimal 3 karakter.',
            'nama.regex' => 'Nama bidang harus memuat huruf dan tidak boleh hanya berisi angka/simbol.',
            'nama.unique' => 'Nama bidang sudah ada, gunakan nama lain.',
            'nama.max' => 'Nama bidang maksimal 255 karakter.',
            'deskripsi.regex' => 'Deskripsi bidang harus memuat teks huruf.',
        ];
    }
}
