<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Http\Requests\Concerns\StripsCurrencyInput;
use App\Models\JenisInventarisData;

// Store Inventaris untuk manajemen aset per item

class StoreInventarisRequest extends FormRequest
{
    use StripsCurrencyInput;
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Inventaris::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nilai_perolehan')) {
            $raw = (string) $this->input('nilai_perolehan');
            // keep digits, dot, comma and minus
            $only = preg_replace('/[^0-9\.,\-]/u', '', $raw);
            if ($only === '') {
                $value = null;
            } else {
                // If both dot and comma present, assume dot=thousand, comma=decimal (id format)
                if (strpos($only, ',') !== false && strpos($only, '.') !== false) {
                    $only = str_replace('.', '', $only);
                    $only = str_replace(',', '.', $only);
                } elseif (strpos($only, ',') !== false) {
                    // comma as decimal separator
                    $only = str_replace(',', '.', $only);
                } else {
                    // only dots (thousand separators) or plain digits
                    $only = str_replace('.', '', $only);
                }

                $value = is_numeric($only) ? (strpos($only, '.') !== false ? (float) $only : (int) $only) : null;
            }

            $this->merge(['nilai_perolehan' => $value]);
        }

        $jenisId = (int) $this->input('jenis_inventaris_id');
        $dynamic = (array) $this->input('dynamic', []);
        if ($jenisId > 0 && !empty($dynamic)) {
            $dynamic = $this->normalizeDynamicNumericFieldsByName($jenisId, $dynamic);
            $this->merge(['dynamic' => $dynamic]);
        }
    }

    public function rules(): array
    {
        return [
            'jenis_inventaris_id' => ['required','integer','exists:jenis_inventaris,id'],
            'jenis_barang' => ['nullable','string','max:100'],
            'nama_barang' => ['nullable','string','max:150'],
            'satuan' => ['nullable','string','max:50'],
            'kode_barang' => ['nullable','string','max:100'],
            'nup' => ['nullable','string','max:100'],
            'tahun_perolehan' => ['nullable','integer','digits:4'],
            'nilai_perolehan' => ['nullable','numeric','min:0'],
            'keterangan' => ['nullable','string','max:1000'],
            'asal_usul' => ['required','string','in:apbdes,perolehan_lain,kekayaan_asli'],
            'pengguna' => ['required','string','max:255'],
            'ruangan_id' => ['nullable','integer','exists:ruangans,id'],
            'kondisi' => ['nullable','string','in:baik,rusak_ringan,rusak_berat'],
            'barcode' => ['nullable','string','max:150'],
            'fotos'   => ['nullable','array','max:5'],
            'fotos.*' => ['image','mimes:jpg,jpeg,png,webp','max:5120'],
            'dynamic'   => ['nullable','array','max:50'],
            'dynamic.*' => ['nullable','string','max:1000'],
            'kib'       => ['nullable','array','max:30'],
            'kib.*'     => ['nullable','string','max:1000'],
            'kir'       => ['nullable','array','max:30'],
            'kir.*'     => ['nullable','string','max:1000'],
            'bulan_perolehan' => ['required','integer','between:1,12'],
        ];
    }

    public function messages(): array
    {
        return [
            'fotos.max' => 'Maksimal upload 5 foto.',
            'fotos.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
            'fotos.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'fotos.*.image' => 'File yang diupload harus berupa gambar.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $uploadedFotos = $this->file('fotos', []);
            if (is_array($uploadedFotos) && count($uploadedFotos) > 0) {
                $totalBytes = 0;
                foreach ($uploadedFotos as $uploadedFoto) {
                    if ($uploadedFoto) {
                        $totalBytes += (int) $uploadedFoto->getSize();
                    }
                }

                if ($totalBytes > 25 * 1024 * 1024) {
                    $validator->errors()->add('fotos', 'Total ukuran seluruh foto maksimal 25 MB.');
                }
            }

            $jenisId = (int) $this->input('jenis_inventaris_id');
            if ($jenisId <= 0) {
                return;
            }

            $jenisFields = JenisInventarisData::where('jenis_inventaris_id', $jenisId)
                ->get(['nama_field', 'label_field', 'field_type']);

            if ($jenisFields->isEmpty()) {
                return;
            }

            $dynamic = (array) $this->input('dynamic', []);

            foreach ($jenisFields as $field) {
                // kode_barang dihandle via select dropdown — skip validasi required di sini
                if ($field->field_type === 'fixed' && $field->nama_field === 'kode_barang') {
                    continue;
                }

                $value = $field->field_type === 'fixed'
                    ? $this->input($field->nama_field)
                    : ($dynamic[$field->nama_field] ?? null);

                if ($this->isBlankValue($value)) {
                    $fieldLabel = $field->label_field ?: $field->nama_field;
                    $errorKey = $field->field_type === 'fixed'
                        ? $field->nama_field
                        : 'dynamic.' . $field->nama_field;

                    $validator->errors()->add($errorKey, $fieldLabel . ' wajib diisi.');
                }
            }
        });
    }

    private function isBlankValue($value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_array($value)) {
            return count($value) === 0;
        }

        return false;
    }
}
