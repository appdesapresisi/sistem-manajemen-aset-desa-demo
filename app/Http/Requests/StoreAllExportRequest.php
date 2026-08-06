<?php

namespace App\Http\Requests;

use App\Models\ExportLog;
use Illuminate\Foundation\Http\FormRequest;

class StoreAllExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ExportLog::class);
    }

    public function rules(): array
    {
        return [
            'format'             => ['required', 'string', 'in:excel,pdf'],
            'tahun'              => ['nullable', 'string', 'regex:/^\d{4}$/'],
            'signature_option'   => ['nullable', 'string', 'in:with,without'],
            'paper_size'         => ['nullable', 'string', 'in:legal,letter,f4'],
            'tanggal_dokumen'    => ['nullable', 'date'],
            'keterangan_dokumen' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN\s\.,&]+$/u'],
            'pejabat_override'   => ['nullable', 'array'],
            'pejabat_override.*' => ['integer', 'exists:pejabats,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'format.required' => 'Format export wajib dipilih.',
            'format.in' => 'Format export tidak valid.',
            'tahun.regex' => 'Format tahun harus 4 digit, contoh: 2026.',
            'signature_option.in' => 'Opsi tanda tangan tidak valid.',
            'tanggal_dokumen.date' => 'Tanggal dokumen tidak valid.',
            'keterangan_dokumen.max' => 'Keterangan tempat maksimal 100 karakter.',
            'keterangan_dokumen.regex' => 'Keterangan tempat hanya boleh huruf, angka, spasi, titik, koma, dan tanda &.',
            'pejabat_override.array' => 'Format pejabat custom tidak valid.',
            'pejabat_override.*.integer' => 'ID pejabat custom harus berupa angka.',
            'pejabat_override.*.exists' => 'Pejabat custom yang dipilih tidak ditemukan.',
        ];
    }
}
