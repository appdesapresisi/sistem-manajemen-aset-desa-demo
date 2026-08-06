<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarisLaporanAsetValue extends Model
{
    protected $fillable = [
        'inventaris_id',
        'bulan_perolehan',
    ];

    protected $casts = [
        'bulan_perolehan' => 'integer',
    ];

    /**
     * Relasi ke Inventaris
     */
    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class);
    }

    /**
     * Get nama bulan dalam bahasa Indonesia
     */
    public function getNamaBulanAttribute(): string
    {
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $bulanMap[$this->bulan_perolehan] ?? '-';
    }

    /**
     * Get semester (1 atau 2)
     */
    public function getSemesterAttribute(): int
    {
        if ($this->bulan_perolehan === null) {
            return 1; // Default semester 1
        }
        return $this->bulan_perolehan <= 6 ? 1 : 2;
    }
}
