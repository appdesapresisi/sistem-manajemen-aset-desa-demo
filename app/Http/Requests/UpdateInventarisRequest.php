<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\Concerns\StripsCurrencyInput;
use App\Models\JenisInventarisData;

class UpdateInventarisRequest extends FormRequest
{
    use StripsCurrencyInput;
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isAdmin());
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nilai_perolehan')) {
            $raw = $this->input('nilai_perolehan');
            if ($raw === null || trim((string) $raw) === '') {
                $value = null;
            } else {
                $normalized = $this->normalizeLocalizedNumberString((string) $raw);
                $value = is_numeric($normalized) ? (float) $normalized : $raw;
            }
            $this->merge(['nilai_perolehan' => $value]);
        }

        $jenisId = (int) $this->input('jenis_inventaris_id');
        $fieldsById = (array) $this->input('fields', []);
        if (!empty($fieldsById)) {
            $fieldsById = array_map(function ($val) {
                return is_string($val) ? trim($val) : $val;
            }, $fieldsById);
            if ($jenisId > 0) {
                $fieldsById = $this->normalizeDynamicNumericFieldsByFieldId($jenisId, $fieldsById);
            }
            $this->merge(['fields' => $fieldsById]);
        }

        foreach (['kib', 'kir'] as $group) {
            $arr = (array) $this->input($group, []);
            if (!empty($arr)) {
                $this->merge([
                    $group => array_map(function ($val) {
                        return is_string($val) ? trim($val) : $val;
                    }, $arr)
                ]);
            }
        }
    }

    public function rules(): array
    {
        $param = $this->route('manajemen_aset') ?? $this->route('manajemen-aset') ?? $this->route('id');
        if (is_object($param)) {
            $currentId = $param->id ?? 0;
        } elseif (is_numeric($param)) {
            $currentId = (int) $param;
        } else {
            $params = $this->route() ? $this->route()->parameters() : [];
            $first = reset($params);
            $currentId = is_object($first) ? ($first->id ?? 0) : (int) $first;
        }

        $kodeBarang = $this->input('kode_barang') ?: \App\Models\Inventaris::where('id', $currentId)->value('kode_barang');
        $tahun = $this->input('tahun_perolehan') ?: \App\Models\Inventaris::where('id', $currentId)->value('tahun_perolehan');

        return [
            'jenis_inventaris_id' => ['required','integer','exists:jenis_inventaris,id'],
            'jenis_barang' => ['nullable','string','max:100'],
            'nama_barang' => ['nullable','string','min:2','max:255','regex:/[a-zA-Z]/'],
            'satuan' => ['nullable','string','max:50','regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s\/\.,\-\(\)]+$/'],
            'kode_barang' => ['nullable','string','max:100'], // read-only di edit form, dipertahankan dari data existing
            'nup' => [
                'nullable',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    if ((int) $value < 1) {
                        $fail('Nomor NUP tidak boleh 0 atau bernilai negatif (harus mulai dari 1).');
                    }
                },
                Rule::unique('inventaris', 'nup')
                    ->ignore($currentId)
                    ->where(function ($query) use ($kodeBarang) {
                        return $query->where('kode_barang', $kodeBarang);
                    }),
            ],
            'tahun_perolehan' => ['nullable','integer','digits:4','min:1900','max:' . (date('Y') + 1)],
            'nilai_perolehan' => ['nullable','numeric','min:0','max:999999999999.99'],
            'keterangan' => ['nullable','string','max:1000'],
            'asal_usul' => ['required','string','in:apbdes,perolehan_lain,kekayaan_asli'],
            'bukti_kepemilikan' => ['nullable','string','max:100'],
            'pengguna' => ['nullable','string','min:3','max:255',"regex:/^[a-zA-Z\s\.,'\-]+$/"],
            'ruangan_id' => ['nullable','integer','exists:ruangans,id'],
            'kondisi' => ['nullable','string','in:baik,rusak_ringan,rusak_berat'],
            // 'status' removed - cannot be changed via form, only via approve()
            'fotos'        => ['nullable','array','max:5'],
            'fotos.*'      => ['image','mimes:jpg,jpeg,png,webp','max:5120'],
            'delete_fotos' => ['nullable','array'],
            'delete_fotos.*' => ['integer','exists:inventaris_fotos,id'],
            'fields' => ['nullable','array','max:50'],
            'fields.*' => ['nullable','string','max:1000'],
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
            'nama_barang.regex' => 'Nama barang harus memuat huruf alfabet (tidak boleh hanya angka/simbol).',
            'satuan.regex' => 'Satuan harus memuat huruf (contoh: Unit, Buah, Lembar, Set, M2).',
            'pengguna.regex' => 'Nama pengguna/pemegang aset hanya boleh berisi huruf dan tanda baca nama (tidak boleh angka).',
            'tahun_perolehan.min' => 'Tahun perolehan minimal tahun 1900.',
            'tahun_perolehan.max' => 'Tahun perolehan tidak boleh lebih dari ' . (date('Y') + 1) . '.',
            'nilai_perolehan.max' => 'Nilai perolehan maksimal adalah Rp 999.999.999.999 (999 Miliar).',
            'nilai_perolehan.numeric' => 'Nilai perolehan harus berupa angka atau nominal rupiah yang valid.',
            'nup.min' => 'Nomor NUP tidak boleh 0 atau bernilai negatif (harus mulai dari 1).',
            'nup.regex' => 'Nomor NUP harus berupa angka bulat positif.',
            'nup.integer' => 'Nomor NUP harus berupa angka bulat positif.',
            'nup.unique' => 'Nomor NUP :input sudah terdaftar untuk kode barang tersebut.',
            'bulan_perolehan.required' => 'Bulan perolehan wajib dipilih.',
            'bulan_perolehan.between' => 'Bulan perolehan tidak valid (harus bulan 1 s/d 12).',
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
                ->get(['id', 'nama_field', 'label_field', 'field_type', 'is_required']);

            if ($jenisFields->isEmpty()) {
                return;
            }

            $dynamicByFieldId = (array) $this->input('fields', []);

            foreach ($jenisFields as $field) {
                // Hanya validasi jika is_required diatur true
                if (!$field->is_required) {
                    continue;
                }

                // kode_barang read-only di edit form — validasi jika required
                if ($field->field_type === 'fixed' && $field->nama_field === 'kode_barang') {
                    if ($this->isBlankValue($this->input('kode_barang'))) {
                        $validator->errors()->add('kode_barang', 'Kode barang wajib diisi.');
                    }
                    continue;
                }

                $value = $field->field_type === 'fixed'
                    ? $this->input($field->nama_field)
                    : ($dynamicByFieldId[$field->id] ?? null);

                if ($this->isBlankValue($value)) {
                    $fieldLabel = $field->label_field ?: $field->nama_field;
                    $errorKey = $field->field_type === 'fixed'
                        ? $field->nama_field
                        : 'fields.' . $field->id;

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
