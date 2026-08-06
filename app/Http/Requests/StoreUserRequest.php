<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Normalisasi checkbox agar konsisten true/false di validator
            'create_pejabat' => $this->boolean('create_pejabat'),
        ]);
    }

    public function authorize()
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    public function rules()
    {
        return [
            'nama' => [
                'required',
                'min:3',
                'max:255',
                'regex:/^[A-Za-z\s\.,]+$/'
            ],

            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/',
                'unique:users,username'
            ],

            // 'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'email' => ['nullable', 'email:rfc', 'max:255', 'unique:users,email'],

            'password' => 'required|string|min:8|confirmed',

            'role' => [
                'required',
                'string',
                'max:100',
                'exists:roles,nama'
            ],

            'is_aktif' => [
                'required',
                'boolean'
            ],

            'create_pejabat' => [
                'boolean'
            ],

            'is_petugas_barang' => [
                'nullable',
                'boolean'
            ],

            'pejabat_nama' => [
                Rule::requiredIf(fn () => $this->boolean('create_pejabat')),
                'nullable',
                'min:3',
                'max:255',
                'regex:/^[A-Za-z\s\.,]+$/'
            ],

            'pejabat_jabatan' => [
                Rule::requiredIf(fn () => $this->boolean('create_pejabat')),
                'nullable',
                'min:3',
                'max:255',
            ],

            'pejabat_tugas' => [
                Rule::requiredIf(fn () => $this->boolean('create_pejabat')),
                'nullable',
                'min:3',
                'max:1000'
            ],

            'pejabat_alamat' => [
                Rule::requiredIf(fn () => $this->boolean('create_pejabat')),
                'nullable',
                'string',
                'max:500'
            ],

            'ttd_path' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:5120' // 5MB
            ],

            'bidang_id' => [
                'required_if:role,kaur,kasi,staf',
                'nullable',
                'integer',
                'exists:bidangs,id'
            ],

            'dinas_id' => [
                'required_if:role,kelian_banjar',
                'nullable',
                'integer',
                'exists:dinas,id'
            ],

            'alamat' => [
                'required',
                'string',
                'max:500'
            ],
        ];
    }

    public function messages()
    {
        return [

            /* ===== Nama ===== */
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'nama.regex' => 'Nama hanya boleh berisi huruf, spasi, titik (.) dan koma (,).',
            'nama.max' => 'Nama terlalu panjang.',

            /* ===== Username ===== */
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username harus diawali huruf, hanya boleh berisi huruf/angka, serta titik (.), strip (-), atau underscore (_) di tengah (tidak boleh berurutan atau di akhir).',
            'username.unique' => 'Username sudah digunakan.',

            /* ===== Email (opsional, tidak aktif) ===== */
            // 'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email terlalu panjang.',

            /* ===== Password ===== */
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',

            /* ===== Role ===== */
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',

            /* ===== Status ===== */
            'is_aktif.required' => 'Status akun wajib ditentukan.',
            'is_aktif.boolean' => 'Status akun tidak valid.',

            /* ===== Pejabat ===== */
            'pejabat_nama.required' => 'Nama pejabat wajib diisi.',
            'pejabat_nama.required_if' => 'Nama pejabat wajib diisi.',
            'pejabat_nama.regex' => 'Nama pejabat hanya boleh berisi huruf, spasi, titik (.) dan koma (,).',

            'pejabat_jabatan.required' => 'Jabatan pejabat wajib diisi.',
            'pejabat_jabatan.required_if' => 'Jabatan pejabat wajib diisi.',

            'pejabat_tugas.required' => 'Tugas pejabat wajib diisi.',
            'pejabat_tugas.required_if' => 'Tugas pejabat wajib diisi.',
            'pejabat_tugas.min' => 'Tugas pejabat minimal 3 karakter.',

            // 'ttd_path.mimes' => 'File tanda tangan harus berupa PNG, JPG, atau PDF.',
            'ttd_path.mimes' => 'File tanda tangan harus berupa PNG dan JPG.',
            'ttd_path.max' => 'Ukuran file tanda tangan maksimal 5MB.',

            /* ===== Bidang (untuk Kaur, Kasi, Staf) ===== */
            'bidang_id.required_if' => 'Bidang wajib dipilih untuk role Kaur, Kasi, atau Staf.',
            'bidang_id.exists' => 'Bidang yang dipilih tidak valid.',

            /* ===== Dinas (untuk Kelian Banjar) ===== */
            'dinas_id.required_if' => 'Dinas wajib dipilih untuk role Kelian Banjar.',
            'dinas_id.exists' => 'Dinas yang dipilih tidak valid.',

            /* ===== Alamat ===== */
            'alamat.required' => 'Alamat lengkap wajib diisi.',
        ];
    }

    public function attributes()
    {
        return [
            'nama' => 'Nama lengkap',
            'username' => 'Username',
            // 'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Konfirmasi password',
            'role' => 'Role',
            'is_aktif' => 'Status akun',
            'pejabat_nama' => 'Nama pejabat',
            'pejabat_jabatan' => 'Jabatan pejabat',
            'pejabat_tugas' => 'Tugas pejabat',
            'ttd_path' => 'File tanda tangan',
            'bidang_id' => 'Bidang',
            'dinas_id' => 'Dinas',
        ];
    }
}

