<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'jenis_inventaris_id',
        'jenis_barang',
        'nama_barang',
        'satuan',
        'kode_barang',
        'nup',
        'tahun_perolehan',
        'nilai_perolehan',
        'keterangan',
        'asal_usul',
        'pengguna',
        'kondisi',
        'barcode',
        'ruangan_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nilai_perolehan' => 'decimal:2',
        'tahun_perolehan' => 'integer',
    ];

    /**
     * Set created_by & updated_by otomatis dari user yang sedang login.
     * Tidak perlu di-set manual di controller, tidak bisa di-inject dari HTTP request.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = auth()->id() ?? 1;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpi');
            \Illuminate\Support\Facades\Cache::forget('dashboard_charts');
        });
    }

    public function jenis()
    {
        return $this->belongsTo(JenisInventaris::class, 'jenis_inventaris_id');
    }

    public function values()
    {
        return $this->hasMany(InventarisValue::class, 'inventaris_id');
    }

    public function approvals()
    {
        // Approval workflow dihapus — return empty collection agar kode yang masih referensikan relasi ini tidak error
        return $this->hasMany(InventarisValue::class, 'inventaris_id')->whereRaw('0=1');
    }

    public function fotos()
    {
        return $this->hasMany(InventarisFoto::class, 'inventaris_id')->orderBy('urutan');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function ruangan()
    {
        return $this->belongsTo(\App\Models\Ruangan::class, 'ruangan_id');
    }

    public function kibValues()
    {
        return $this->hasMany(\App\Models\InventarisKibValue::class, 'inventaris_id');
    }

    /**
     * Ambil nilai KIB berdasarkan key.
     */
    public function getKibValue(string $key): ?string
    {
        return $this->kibValues->firstWhere('kib_field_key', $key)?->value;
    }

    public function kirValues()
    {
        return $this->hasMany(\App\Models\InventarisKirValue::class, 'inventaris_id');
    }

    /**
     * Relasi ke Laporan Aset Value (bulan perolehan untuk laporan semester)
     */
    public function laporanAsetValue()
    {
        return $this->hasOne(\App\Models\InventarisLaporanAsetValue::class, 'inventaris_id');
    }

    /**
     * Ambil nilai KIR berdasarkan key.
     */
    public function getKirValue(string $key): ?string
    {
        return $this->kirValues->firstWhere('kir_field_key', $key)?->value;
    }

    /**
     * Get bulan perolehan untuk laporan semester (1-12 atau null)
     */
    public function getBulanPerolehan(): ?int
    {
        return $this->laporanAsetValue?->bulan_perolehan;
    }

    /**
     * Nama tampilan untuk ditampilkan di UI (index, show, edit, notifikasi, dll).
     *
     * Urutan prioritas saat ini (AKTIF):
     *   1. nama_barang tidak null  → langsung pakai
     *   2. Fallback                → nilai field pertama berdasarkan order terendah di inventaris_values
     *
     * OPSI A (BELUM AKTIF — uncomment jika kolom is_name_field sudah ditambahkan ke DB):
     *   Gantikan urutan 2 di atas dengan lookup eksplisit ke field yang ditandai is_name_field = 1.
     *   Ini jauh lebih reliable untuk jenis inventaris dinamis yang ditambahkan di masa depan.
     *
     * Prasyarat OPSI A:
     *   1. Jalankan SQL berikut di SQLyog / tool DB:
     *        ALTER TABLE jenis_inventaris_data
     *            ADD COLUMN is_name_field TINYINT(1) NOT NULL DEFAULT 0 AFTER is_required;
     *   2. Tandai field yang menjadi "nama" untuk tiap jenis inventaris, contoh:
     *        UPDATE jenis_inventaris_data SET is_name_field = 1 WHERE nama_field = 'nama_barang';
     *        UPDATE jenis_inventaris_data SET is_name_field = 1 WHERE nama_field = 'jenis_bangunan_bangunan';
     *        UPDATE jenis_inventaris_data SET is_name_field = 1 WHERE nama_field = 'jenis_tanah';
     *        UPDATE jenis_inventaris_data SET is_name_field = 1 WHERE nama_field = 'jenis_jalan_irigasi_dan_jaringan';
     *   3. Tambahkan 'is_name_field' ke $fillable & $casts di JenisInventarisData.php (lihat komentar di sana)
     *   4. Uncomment blok OPSI A di bawah dan comment baris $firstValue (fallback saat ini)
     */
    public function getDisplayNameAttribute(): ?string
    {
        // --- PRIORITAS 1: gunakan nama_barang jika tersedia ---
        if (!is_null($this->nama_barang)) {
            return $this->nama_barang;
        }

        // --- PRIORITAS 2 (AKTIF): ambil nilai field pertama berdasarkan order terendah ---
        // Bekerja untuk sekarang karena field "pengganti nama" kebetulan ada di order terkecil.
        // Gantikan dengan OPSI A di bawah jika ingin lebih eksplisit dan aman jangka panjang.
        $firstValue = $this->values
            ->filter(fn($v) => $v->field !== null)
            ->sortBy(fn($v) => $v->field->order ?? 999)
            ->first();

        return $firstValue?->value ?? null;

        // =====================================================================
        // OPSI A — Aktifkan blok ini setelah kolom is_name_field dibuat di DB
        // Jika diaktifkan: comment 3 baris $firstValue + return di atas,
        // lalu uncomment seluruh blok di bawah ini.
        // =====================================================================
        //
        // // Cari value dari field yang secara eksplisit ditandai sebagai "nama tampilan"
        // // (is_name_field = 1 di tabel jenis_inventaris_data)
        // $namedField = $this->values
        //     ->filter(fn($v) => $v->field !== null && !empty($v->field->is_name_field))
        //     ->sortBy(fn($v) => $v->field->order ?? 999)
        //     ->first();
        //
        // if ($namedField) {
        //     return $namedField->value;
        // }
        //
        // // Fallback terakhir: field text pertama by order (jika is_name_field belum di-set)
        // $firstValue = $this->values
        //     ->filter(fn($v) => $v->field !== null && $v->field->tipe === 'text')
        //     ->sortBy(fn($v) => $v->field->order ?? 999)
        //     ->first();
        //
        // return $firstValue?->value ?? null;
    }

    public function getKondisiLabelAttribute(): ?string
    {
        if (!$this->kondisi) {
            return null;
        }

        return str_replace('_', ' ', $this->kondisi);
    }

    public function setKondisiAttribute($value): void
    {
        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            $normalized = str_replace(' ', '_', $normalized);
            $this->attributes['kondisi'] = $normalized;
            return;
        }

        $this->attributes['kondisi'] = $value;
    }
}
