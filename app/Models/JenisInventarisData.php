<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisInventarisData extends Model
{
    use HasFactory;

    protected $table = 'jenis_inventaris_data';

    // Available fixed fields yang bisa digunakan
    const AVAILABLE_FIXED_FIELDS = [
        'kode_barang' => ['label' => 'Kode Barang', 'type' => 'text', 'order' => 2],
        'nama_barang' => ['label' => 'Jenis Barang / Nama Barang', 'type' => 'text', 'order' => 3],
        'nup' => ['label' => 'NUP', 'type' => 'text', 'order' => 4],
        'tahun_perolehan' => ['label' => 'Tahun Perolehan', 'type' => 'year', 'order' => 5],
        'nilai_perolehan' => ['label' => 'Nilai Perolehan', 'type' => 'number', 'order' => 6],
        'kondisi' => ['label' => 'Kondisi', 'type' => 'select', 'order' => 7],
        'keterangan' => ['label' => 'Keterangan', 'type' => 'textarea', 'order' => 8],
    ];

    protected $fillable = [
        'jenis_inventaris_id',
        'field_type',
        'fixed_field_name',
        'nama_field',
        'label_field',
        'tipe',
        'dropdown_id',
        'is_required',
        'include_in_export',
        'order',
    ];

    protected $casts = [
        'is_required'       => 'boolean',
        'include_in_export' => 'boolean',
        // is_name_field — uncomment setelah kolom ditambahkan ke DB:
        // 'is_name_field' => 'boolean',
    ];

    public function dropdown()
    {
        return $this->belongsTo(\App\Models\Dropdown::class, 'dropdown_id');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisInventaris::class, 'jenis_inventaris_id');
    }

    /**
     * Cek apakah field ini merupakan field mata uang (Rupiah).
     * Digunakan secara konsisten di form, tampilan, dan export.
     * Threshold keyword sama dengan sumTargetScore() di InventarisExport.
     */
    public static function isCurrencyField(?string $fixedFieldName, ?string $tipe, ?string $nama, ?string $label): bool
    {
        if ($fixedFieldName === 'nilai_perolehan') {
            return true;
        }
        // Tangani field number MAUPUN text yang memiliki label/nama bermakna uang.
        // tipe=text diizinkan untuk backward-compat konfigurasi lama (contoh: Bangunan "Nilai" tipe=text).
        if (in_array($tipe, ['number', 'text'], true)) {
            $haystack = strtolower(trim(($nama ?? '') . ' ' . ($label ?? '')));
            if (preg_match('/\b(nilai|harga|rupiah)\b/', $haystack)
                && !preg_match('/\b(luas|tahun|unit)\b/', $haystack)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get value dari inventaris berdasarkan field type
     * 
     * @param \App\Models\Inventaris $inventaris
     * @return mixed
     */
    public function getValueFromInventaris($inventaris)
    {
        if ($this->field_type === 'fixed') {
            // Handle relasi (contoh: jenis.nama)
            if (str_contains($this->fixed_field_name, '.')) {
                [$relation, $column] = explode('.', $this->fixed_field_name, 2);
                return $inventaris->$relation->$column ?? '-';
            }
            // Direct column dari table inventaris
            return $inventaris->{$this->fixed_field_name} ?? '-';
        } else {
            // Custom field dari inventaris_values
            $value = $inventaris->values->firstWhere('jenis_inventaris_data_id', $this->id);
            return $value ? $value->value : '-';
        }
    }
}
