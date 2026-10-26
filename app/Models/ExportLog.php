<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    use HasFactory;

    protected $table = 'export_logs';

    protected $fillable = [
        'user_id',
        'inventaris_id',
        'jenis_inventaris_id',
        'pejabat_id',
        'tipe_export',
        'nama_file',
        'pejabat_override',
        'filters',
    ];

    // Timestamps NOW ENABLED (database sudah punya created_at & updated_at)
    // public $timestamps = false; // ← REMOVED

    protected $casts = [
        'pejabat_override' => 'array',
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope query untuk mengecualikan riwayat ekspor yang dilakukan oleh Super Developer / Platform Owner.
     */
    public function scopeExcludeSuperDevelopers($query)
    {
        $devUsernames = User::getSuperDeveloperUsernames();

        return $query->where(function ($q) use ($devUsernames) {
            $q->whereNull('user_id')
              ->orWhereDoesntHave('user', function ($uq) use ($devUsernames) {
                  $uq->where(function ($userSub) use ($devUsernames) {
                      if (!empty($devUsernames)) {
                          $userSub->whereIn('username', $devUsernames);
                      }
                      $userSub->orWhereHas('role', function ($rq) {
                          $rq->whereIn('nama', ['super_developer', 'developer', 'owner']);
                      })
                      ->orWhereRaw("LOWER(nama) LIKE '%super developer%'")
                      ->orWhereRaw("LOWER(nama) LIKE '%platform owner%'");
                  });
              });
        });
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function jenisInventaris()
    {
        return $this->belongsTo(JenisInventaris::class, 'jenis_inventaris_id');
    }

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'pejabat_id');
    }

    /**
     * Resolusi nama jenis laporan / ekspor untuk tampilan UI dan Dashboard
     */
    public function getJenisLabelAttribute(): string
    {
        if ($this->jenis_inventaris_id && $this->jenisInventaris) {
            return $this->jenisInventaris->nama;
        }

        $filters = is_array($this->filters) ? $this->filters : [];

        if (!empty($filters['virtual_export_as'])) {
            return (string) $filters['virtual_export_as'];
        }

        if (!empty($filters['kib_type'])) {
            $kt = strtoupper((string) $filters['kib_type']);
            $kibLabels = [
                'KIB-A' => 'KIB A (Tanah)',
                'KIB-B' => 'KIB B (Peralatan & Mesin)',
                'KIB-C' => 'KIB C (Gedung & Bangunan)',
                'KIB-D' => 'KIB D (Jalan, Irigasi & Jaringan)',
                'KIB-E' => 'KIB E (Aset Tetap Lainnya)',
                'KIB-F' => 'KIB F (Konstruksi Dalam Pengerjaan)',
            ];
            return $kibLabels[$kt] ?? $kt;
        }

        $mode = $filters['mode'] ?? '';
        if ($mode === 'kir') {
            return 'KIR (Kartu Inventaris Ruangan)';
        }
        if ($mode === 'buku_inventaris_standalone') {
            return 'Buku Inventaris Desa';
        }
        if ($mode === 'laporan_aset') {
            return 'Laporan Aset Desa';
        }
        if ($mode === 'all_jenis') {
            return 'Semua Jenis Inventaris';
        }

        return 'Semua Jenis';
    }
}
