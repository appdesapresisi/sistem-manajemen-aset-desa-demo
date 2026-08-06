<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama',
        'kode',
        // 'lokasi',
        'deskripsi',
        'penanggung_jawab_id',
    ];

    /**
     * Penanggung jawab ruangan (relasi ke User).
     */
    public function penanggungJawab()
    {
        return $this->belongsTo(\App\Models\User::class, 'penanggung_jawab_id');
    }

    /**
     * Inventaris yang berada di ruangan ini.
     */
    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'ruangan_id');
    }

    /**
     * Accessor: inisial dari nama ruangan (untuk avatar).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->nama));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->nama, 0, 2));
    }
}
