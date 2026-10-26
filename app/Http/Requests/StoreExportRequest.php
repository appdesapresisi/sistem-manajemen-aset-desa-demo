<?php

namespace App\Http\Requests;

use App\Models\ExportLog;
use Illuminate\Foundation\Http\FormRequest;

class StoreExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ExportLog::class);
    }

    public function rules(): array
    {
        return [
            'jenis_inventaris_id' => ['required', 'string', 'regex:/^\d+$/'],
            'format'             => ['required', 'string', 'in:excel,pdf'],
            'tahun'              => ['nullable', 'string', 'regex:/^(\d{4}|all)$/i'],
            'signature_option'   => ['nullable', 'string', 'in:with,without'],
            'paper_size'         => ['nullable', 'string', 'in:legal,letter,f4,a4'],
            'tanggal_dokumen'    => ['nullable', 'date'],
            'keterangan_dokumen' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN\s\.,&]+$/u'],
            'pejabat_override'   => ['nullable', 'array'],
            'pejabat_override.*' => ['integer', 'exists:pejabats,id'],
            // Informasi Dokumen (Lampiran & Berita Acara)
            'doc_lampiran'       => ['nullable', 'string', 'max:50'],
            'doc_ba_nomor'       => ['nullable', 'string', 'max:100'],
            'doc_ba_tanggal'     => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_inventaris_id.required' => 'Jenis inventaris wajib dipilih.',
            'jenis_inventaris_id.regex' => 'Jenis inventaris tidak valid.',
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
            'doc_lampiran.max' => 'Lampiran maksimal 50 karakter.',
            'doc_ba_nomor.max' => 'Nomor Berita Acara maksimal 100 karakter.',
            'doc_ba_tanggal.date' => 'Tanggal Berita Acara tidak valid.',
        ];
    }
}
