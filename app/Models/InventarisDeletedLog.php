<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisDeletedLog extends Model
{
    protected $table = 'inventaris_deleted_log';

    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'nup',
        'nama_barang',
        'jenis_inventaris_nama',
        'merk_type',
        'satuan',
        'nilai_perolehan',
        'tahun_perolehan',
        'ruangan_nama',
        'nomor_sk',
        'alasan_penghapusan',
        'keterangan',
        'asset_data_snapshot',
        'dokumen_pdf_path',
        'dokumen_pdf_name',
        'tanggal_hapus',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'asset_data_snapshot' => 'array',
        'tanggal_hapus'       => 'date',
        'deleted_at'          => 'datetime',
        'nilai_perolehan'     => 'decimal:2',
        'tahun_perolehan'     => 'integer',
    ];

    private static ?\Illuminate\Support\Collection $allJenisCache = null;

    public static function clearJenisCache(): void
    {
        static::$allJenisCache = null;
    }

    protected static function getAllJenis(): \Illuminate\Support\Collection
    {
        if (static::$allJenisCache === null) {
            static::$allJenisCache = JenisInventaris::all();
        }
        return static::$allJenisCache;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getJenisModel()
    {
        $all = static::getAllJenis();

        if (!empty($this->jenis_inventaris_nama)) {
            $found = $all->firstWhere('nama', $this->jenis_inventaris_nama);
            if ($found) {
                return $found;
            }
        }

        // Coba cocokkan berdasarkan kode barang prefix jika ada
        if (!empty($this->kode_barang)) {
            if (str_starts_with($this->kode_barang, '1.01') || str_starts_with($this->kode_barang, '01.')) {
                $found = $all->first(fn($j) => stripos($j->nama, 'Tanah') !== false);
                if ($found) return $found;
            } elseif (str_starts_with($this->kode_barang, '1.02') || str_starts_with($this->kode_barang, '3.05') || str_starts_with($this->kode_barang, '02.')) {
                $found = $all->first(fn($j) => stripos($j->nama, 'Peralatan') !== false);
                if ($found) return $found;
            } elseif (str_starts_with($this->kode_barang, '1.03') || str_starts_with($this->kode_barang, '03.')) {
                $found = $all->first(fn($j) => stripos($j->nama, 'Bangunan') !== false || stripos($j->nama, 'Gedung') !== false);
                if ($found) return $found;
            } elseif (str_starts_with($this->kode_barang, '1.04') || str_starts_with($this->kode_barang, '04.')) {
                $found = $all->first(fn($j) => stripos($j->nama, 'Jalan') !== false || stripos($j->nama, 'Jaringan') !== false);
                if ($found) return $found;
            } elseif (str_starts_with($this->kode_barang, '1.05') || str_starts_with($this->kode_barang, '05.')) {
                $found = $all->first(fn($j) => stripos($j->nama, 'Lainnya') !== false);
                if ($found) return $found;
            }
        }

        // Fallback default master jenis inventaris
        return $all->firstWhere('nama', 'Aset Tetap Lainnya') ?? $all->first();
    }

    public function getJenisNamaAttribute(): string
    {
        if (!empty($this->jenis_inventaris_nama)) {
            return $this->jenis_inventaris_nama;
        }
        $jenis = $this->getJenisModel();
        return $jenis ? $jenis->nama : 'Aset Tetap Lainnya';
    }

    public function getJenisIconAttribute(): string
    {
        $jenis = $this->getJenisModel();
        return $jenis ? $jenis->icon : 'layers';
    }

    public function getJenisColorsAttribute(): array
    {
        $jenis = $this->getJenisModel();
        return $jenis ? $jenis->color_gradient : ['#14b8a6', '#0d9488'];
    }
}
