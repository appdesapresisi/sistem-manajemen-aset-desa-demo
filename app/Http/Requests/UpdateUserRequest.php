<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isSuperAdmin());
    }

    public function rules()
    {
        $userId = $this->route('user') instanceof \App\Models\User
            ? $this->route('user')->id
            : ($this->route('user') ?? $this->route('id'));

        return [

            'nama' => [
                'required',
                'min:3',
                'max:255',
                "regex:/^[a-zA-Z\s\.,'\-]+$/"
            ],

            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/',
                Rule::unique('users', 'username')->ignore($userId),
                Rule::notIn(\App\Models\User::getSuperDeveloperUsernames()),
            ],

            // 'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users','email')->ignore($userId)],
            'email' => ['nullable', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($userId)],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],

            'role' => [
                'required',
                'string',
                'max:100',
                'exists:roles,nama',
                Rule::notIn(['super_developer']),
            ],

            'is_aktif' => [
                'required',
                'boolean'
            ],

            'create_pejabat' => [
                'nullable',
                'boolean'
            ],

            'is_petugas_barang' => [
                'nullable',
                'boolean'
            ],

            'pejabat_nama' => [
                'required_if:create_pejabat,1',
                'nullable',
                'min:3',
                'max:255',
                "regex:/^[a-zA-Z\s\.,'\-]+$/"
            ],

            'pejabat_jabatan' => [
                'required_if:create_pejabat,1',
                'nullable',
                'min:3',
                'max:255',
                'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/'
            ],

            'pejabat_tugas' => [
                'nullable',
                'max:1000'
            ],

            'pejabat_alamat' => [
                'required_if:create_pejabat,1',
                'nullable',
                'string',
                'max:500'
            ],

            'ttd_path' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp',
                'max:5120'
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

            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'nama.regex' => 'Nama hanya boleh berisi huruf, spasi, titik (.) dan koma (,).',
            'nama.max' => 'Nama terlalu panjang.',

            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username harus diawali huruf, hanya boleh berisi huruf/angka, serta titik (.), strip (-), atau underscore (_) di tengah (tidak boleh berurutan atau di akhir).',
            'username.unique' => 'Username sudah digunakan user lain.',
            'username.not_in' => 'Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan.',

            // 'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan user lain.',
            'email.max' => 'Email terlalu panjang.',

            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',

            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',

            'is_aktif.required' => 'Status akun wajib ditentukan.',
            'is_aktif.boolean' => 'Status akun tidak valid.',

            'pejabat_nama.required_if' => 'Nama pejabat wajib diisi.',
            'pejabat_nama.regex' => 'Nama pejabat hanya boleh berisi huruf, spasi, titik (.) dan koma (,).',

            'pejabat_jabatan.required_if' => 'Jabatan pejabat wajib diisi.',

            'ttd_path.mimes' => 'File tanda tangan harus berupa gambar (PNG, JPG, JPEG, WEBP).',
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

