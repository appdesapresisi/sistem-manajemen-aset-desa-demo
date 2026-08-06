<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $table = 'pejabats';

    protected $fillable = [
        'nama',
        'jabatan',
        'tugas',
        'alamat',
        'ttd_path',
        'ttd_type',
        'is_aktif',
        'is_petugas_barang',
    ];

    protected $casts = [
        'is_aktif'           => 'boolean',
        'is_petugas_barang'  => 'boolean',
    ];

    public function jenisDefaults()
    {
        return $this->hasMany(JenisInventarisPejabat::class, 'pejabat_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'pejabat_id');
    }

    /**
     * Accessor: Generate avatar color based on name
     */
    public function getAvatarColorAttribute()
    {
        $colors = [
            '#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', 
            '#10b981', '#06b6d4', '#3b82f6', '#ef4444'
        ];
        $hash = crc32($this->nama ?? 'pejabat');
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Accessor: Generate initials from name
     */
    public function getInitialsAttribute()
    {
        $name = $this->nama ?? 'Pejabat';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Accessor: Get badge class based on jabatan
     */
    public function getBadgeClassAttribute()
    {
        $jabatan = strtolower($this->jabatan ?? '');
        if (str_contains($jabatan, 'kepala')) return 'kepala';
        if (str_contains($jabatan, 'sekretaris')) return 'sekretaris';
        if (str_contains($jabatan, 'kaur') || str_contains($jabatan, 'kepala urusan')) return 'kaur';
        if (str_contains($jabatan, 'kasi') || str_contains($jabatan, 'kepala seksi')) return 'kasi';
        return 'default';
    }

    /**
     * Accessor: Get badge icon based on jabatan
     */
    public function getBadgeIconAttribute()
    {
        $jabatan = strtolower($this->jabatan ?? '');
        if (str_contains($jabatan, 'kepala')) return 'award';
        if (str_contains($jabatan, 'sekretaris')) return 'briefcase';
        if (str_contains($jabatan, 'kaur')) return 'file-text';
        if (str_contains($jabatan, 'kasi')) return 'folder';
        return 'user';
    }
}
