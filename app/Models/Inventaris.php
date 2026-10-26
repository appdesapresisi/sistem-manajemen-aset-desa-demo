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
        'bukti_kepemilikan',
        'pengguna',
        'kondisi',
        'status',
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
                $model->created_by = auth()->id() ?? \App\Models\User::query()->value('id') ?? 1;
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

    /**
     * Mutator status untuk sanitasi enum status inventaris.
     */
    public function setStatusAttribute($value): void
    {
        $allowed = ['draft', 'pending', 'disetujui', 'ditolak'];
        $val = strtolower((string)$value);
        if (in_array($val, $allowed, true)) {
            $this->attributes['status'] = $val;
        } elseif ($val === 'aktif' || $val === 'active' || $val === 'approved') {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'draft';
        }
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
     * Resolve Merk / Type dari berbagai sumber (KIB, KIR, atau Dynamic Field Values)
     */
    public function getMerkTypeAttribute(): string
    {
        // 1. Dari KIB value
        $kib = trim((string)($this->getKibValue('merk_type') ?? ''));
        if ($kib !== '' && $kib !== '-') return $kib;

        // 2. Dari KIR value
        $kir = trim((string)($this->getKirValue('kir_merk_tipe') ?? ''));
        if ($kir !== '' && $kir !== '-') return $kir;

        // 3. Dari dynamic fields (values)
        if ($this->relationLoaded('values') || $this->values()->exists()) {
            foreach ($this->values as $val) {
                if ($val->field) {
                    $lbl = strtolower($val->field->label_field ?? $val->field->nama_field ?? '');
                    if (str_contains($lbl, 'merk') || str_contains($lbl, 'merek') || str_contains($lbl, 'type') || str_contains($lbl, 'tipe') || str_contains($lbl, 'model')) {
                        $v = trim((string)($val->value ?? ''));
                        if ($v !== '' && $v !== '-') return $v;
                    }
                }
            }
        }

        return '-';
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

    /**
     * Scope untuk memfilter inventaris berdasarkan kelengkapan data wajib.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type ('lengkap' | 'tidak_lengkap')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKelengkapan($query, string $type)
    {
        if ($type === 'tidak_lengkap') {
            return $query->where(function($q) {
                $q->whereNull('nama_barang')->orWhere('nama_barang', '')
                  ->orWhereNull('kode_barang')->orWhere('kode_barang', '')
                  ->orWhereNull('nup')->orWhere('nup', '')
                  ->orWhereNull('tahun_perolehan')->orWhere('tahun_perolehan', '<=', 0)
                  ->orWhereNull('asal_usul')->orWhere('asal_usul', '')
                  ->orWhere(function($kq) {
                      $kq->where(function($subK) {
                          $subK->whereNull('kondisi')->orWhere('kondisi', '');
                      })->whereExists(function($sub) {
                          $sub->selectRaw(1)
                              ->from('jenis_inventaris_data')
                              ->whereColumn('jenis_inventaris_data.jenis_inventaris_id', 'inventaris.jenis_inventaris_id')
                              ->where(function($fn) {
                                  $fn->where('jenis_inventaris_data.nama_field', 'kondisi')
                                     ->orWhere('jenis_inventaris_data.fixed_field_name', 'kondisi');
                              })
                              ->where('jenis_inventaris_data.is_required', 1);
                      });
                  })
                  ->orWhere(function($rq) {
                      $rq->whereNull('ruangan_id')
                         ->whereExists(function($sub) {
                             $sub->selectRaw(1)
                                 ->from('jenis_inventaris')
                                 ->whereColumn('jenis_inventaris.id', 'inventaris.jenis_inventaris_id')
                                 ->where('jenis_inventaris.requires_ruangan', 1);
                         });
                  })
                  ->orWhereExists(function($sub) {
                      $sub->selectRaw(1)
                          ->from('jenis_inventaris_data')
                          ->whereColumn('jenis_inventaris_data.jenis_inventaris_id', 'inventaris.jenis_inventaris_id')
                          ->where('jenis_inventaris_data.is_required', 1)
                          ->where('jenis_inventaris_data.field_type', 'custom')
                          ->whereNotExists(function($valSub) {
                              $valSub->selectRaw(1)
                                  ->from('inventaris_values')
                                  ->whereColumn('inventaris_values.inventaris_id', 'inventaris.id')
                                  ->whereColumn('inventaris_values.jenis_inventaris_data_id', 'jenis_inventaris_data.id')
                                  ->whereNotNull('inventaris_values.value')
                                  ->whereRaw("TRIM(inventaris_values.value) != ''");
                          });
                  });
            });
        } elseif ($type === 'lengkap') {
            return $query->where(function($q) {
                $q->whereNotNull('nama_barang')->where('nama_barang', '!=', '')
                  ->whereNotNull('kode_barang')->where('kode_barang', '!=', '')
                  ->whereNotNull('nup')->where('nup', '!=', '')
                  ->whereNotNull('tahun_perolehan')->where('tahun_perolehan', '>', 0)
                  ->whereNotNull('asal_usul')->where('asal_usul', '!=', '')
                  ->whereNot(function($kq) {
                      $kq->where(function($subK) {
                          $subK->whereNull('kondisi')->orWhere('kondisi', '');
                      })->whereExists(function($sub) {
                          $sub->selectRaw(1)
                              ->from('jenis_inventaris_data')
                              ->whereColumn('jenis_inventaris_data.jenis_inventaris_id', 'inventaris.jenis_inventaris_id')
                              ->where(function($fn) {
                                  $fn->where('jenis_inventaris_data.nama_field', 'kondisi')
                                     ->orWhere('jenis_inventaris_data.fixed_field_name', 'kondisi');
                              })
                              ->where('jenis_inventaris_data.is_required', 1);
                      });
                  })
                  ->whereNot(function($rq) {
                      $rq->whereNull('ruangan_id')
                         ->whereExists(function($sub) {
                             $sub->selectRaw(1)
                                 ->from('jenis_inventaris')
                                 ->whereColumn('jenis_inventaris.id', 'inventaris.jenis_inventaris_id')
                                 ->where('jenis_inventaris.requires_ruangan', 1);
                         });
                  })
                  ->whereNotExists(function($sub) {
                      $sub->selectRaw(1)
                          ->from('jenis_inventaris_data')
                          ->whereColumn('jenis_inventaris_data.jenis_inventaris_id', 'inventaris.jenis_inventaris_id')
                          ->where('jenis_inventaris_data.is_required', 1)
                          ->where('jenis_inventaris_data.field_type', 'custom')
                          ->whereNotExists(function($valSub) {
                              $valSub->selectRaw(1)
                                  ->from('inventaris_values')
                                  ->whereColumn('inventaris_values.inventaris_id', 'inventaris.id')
                                  ->whereColumn('inventaris_values.jenis_inventaris_data_id', 'jenis_inventaris_data.id')
                                  ->whereNotNull('inventaris_values.value')
                                  ->whereRaw("TRIM(inventaris_values.value) != ''");
                          });
                  });
            });
        }
        return $query;
    }

    /**
     * Cek apakah seluruh data wajib pada aset ini sudah terisi lengkap.
     */
    public function isDataLengkap(): bool
    {
        if (empty(trim($this->nama_barang ?? ''))) return false;
        if (empty(trim($this->kode_barang ?? ''))) return false;
        if (is_null($this->nup) || trim((string)$this->nup) === '') return false;
        if (empty($this->tahun_perolehan) || (int)$this->tahun_perolehan <= 0) return false;
        if (empty(trim($this->asal_usul ?? ''))) return false;

        // Cek kondisi hanya jika field kondisi dikonfigurasi sebagai required pada jenis inventaris ini
        if ($this->jenis_inventaris_id) {
            $isKondisiRequired = \App\Models\JenisInventarisData::where('jenis_inventaris_id', $this->jenis_inventaris_id)
                ->where(function($q) {
                    $q->where('nama_field', 'kondisi')
                      ->orWhere('fixed_field_name', 'kondisi');
                })
                ->where('is_required', 1)
                ->exists();

            if ($isKondisiRequired && empty(trim($this->kondisi ?? ''))) {
                return false;
            }
        }

        if ($this->jenis && $this->jenis->requires_ruangan && is_null($this->ruangan_id)) {
            return false;
        }

        if ($this->jenis_inventaris_id) {
            $requiredCustom = \App\Models\JenisInventarisData::where('jenis_inventaris_id', $this->jenis_inventaris_id)
                ->where('is_required', 1)
                ->where('field_type', 'custom')
                ->get();

            if ($requiredCustom->isNotEmpty()) {
                $values = $this->relationLoaded('values') ? $this->values : $this->values()->get();
                foreach ($requiredCustom as $rf) {
                    $val = $values->firstWhere('jenis_inventaris_data_id', $rf->id);
                    if (!$val || trim((string)($val->value ?? '')) === '') {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Dapatkan daftar label field wajib yang belum terisi lengkap.
     */
    public function getMissingRequiredFields(): array
    {
        $missing = [];

        if (empty(trim($this->nama_barang ?? ''))) $missing[] = 'Nama Barang';
        if (empty(trim($this->kode_barang ?? ''))) $missing[] = 'Kode Barang';
        if (is_null($this->nup) || trim((string)$this->nup) === '') $missing[] = 'NUP';
        if (empty($this->tahun_perolehan) || (int)$this->tahun_perolehan <= 0) $missing[] = 'Tahun Perolehan';
        if (empty(trim($this->asal_usul ?? ''))) $missing[] = 'Asal Usul';

        // Cek kondisi hanya jika field kondisi dikonfigurasi sebagai required pada jenis inventaris ini
        if ($this->jenis_inventaris_id) {
            $isKondisiRequired = \App\Models\JenisInventarisData::where('jenis_inventaris_id', $this->jenis_inventaris_id)
                ->where(function($q) {
                    $q->where('nama_field', 'kondisi')
                      ->orWhere('fixed_field_name', 'kondisi');
                })
                ->where('is_required', 1)
                ->exists();

            if ($isKondisiRequired && empty(trim($this->kondisi ?? ''))) {
                $missing[] = 'Kondisi';
            }
        }

        if ($this->jenis && $this->jenis->requires_ruangan && is_null($this->ruangan_id)) {
            $missing[] = 'Ruangan';
        }

        if ($this->jenis_inventaris_id) {
            $requiredCustom = \App\Models\JenisInventarisData::where('jenis_inventaris_id', $this->jenis_inventaris_id)
                ->where('is_required', 1)
                ->where('field_type', 'custom')
                ->get();

            if ($requiredCustom->isNotEmpty()) {
                $values = $this->relationLoaded('values') ? $this->values : $this->values()->get();
                foreach ($requiredCustom as $rf) {
                    $val = $values->firstWhere('jenis_inventaris_data_id', $rf->id);
                    if (!$val || trim((string)($val->value ?? '')) === '') {
                        $missing[] = $rf->label_field ?: $rf->nama_field;
                    }
                }
            }
        }

        return $missing;
    }

    /**
     * Generate unique barcode string for an inventaris item.
     */
    public static function generateBarcode(): string
    {
        $year = date('Y');
        $attempt = 0;
        do {
            $candidate = 'INV-' . $year . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            $exists = static::where('barcode', $candidate)->exists();
            $attempt++;
        } while ($exists && $attempt < 10);

        if ($exists) {
            $candidate = 'INV-' . $year . '-' . strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
        }

        return $candidate;
    }
}
