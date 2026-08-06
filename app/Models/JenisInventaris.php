<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisInventaris extends Model
{
    use HasFactory;

    protected $table = 'jenis_inventaris';

    protected $fillable = [
        'nama',
        'deskripsi',
        'icon_mode',
        'custom_icon',
        'custom_color',
        'requires_ruangan',
        'kib_type',
    ];

    protected $casts = [
        'requires_ruangan' => 'boolean',
    ];

    // Appends untuk menambahkan icon dan color ke JSON
    protected $appends = ['icon', 'color_gradient'];

    public function fields()
    {
        return $this->hasMany(JenisInventarisData::class, 'jenis_inventaris_id');
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'jenis_inventaris_id');
    }

    public function pejabatDefaults()
    {
        return $this->hasMany(JenisInventarisPejabat::class, 'jenis_inventaris_id');
    }

    /**
     * Resolve icon berdasarkan kata kunci dalam nama jenis inventaris.
     * Menggunakan Feather Icons — universal dan konsisten.
     * Icon selalu berwarna putih (diatur oleh CSS), bg warna random via color pool.
     */
    private static function resolveIconByKeywords(string $nama): string
    {
        $name = mb_strtolower($nama);

        // Ordered dari paling spesifik ke paling umum
        $keywordMap = [
            // Kendaraan & Transportasi
            'kendaraan'     => 'truck',
            'mobil'         => 'truck',
            'motor'         => 'truck',
            'truk'          => 'truck',
            'transportasi'  => 'truck',
            'angkutan'      => 'truck',

            // Bangunan & Konstruksi
            'bangunan'      => 'home',
            'gedung'        => 'home',
            'kantor'        => 'home',
            'rumah'         => 'home',
            'gudang'        => 'home',
            'konstruksi'    => 'home',

            // Tanah & Lahan
            'tanah'         => 'map-pin',
            'lahan'         => 'map-pin',
            'kavling'       => 'map-pin',
            'persil'        => 'map-pin',

            // Jalan & Infrastruktur Fisik
            'jalan'         => 'navigation',
            'jembatan'      => 'navigation',
            'infrastruktur' => 'navigation',

            // Irigasi & Air
            'irigasi'       => 'droplet',
            'air'           => 'droplet',
            'saluran'       => 'droplet',
            'drainase'      => 'droplet',
            'sanitasi'      => 'droplet',

            // Jaringan & Komunikasi
            'jaringan'      => 'share-2',
            'komunikasi'    => 'share-2',
            'telepon'       => 'phone',
            'radio'         => 'radio',
            'internet'      => 'wifi',

            // Peralatan & Mesin
            'peralatan'     => 'tool',
            'mesin'         => 'tool',
            'alat'          => 'tool',
            'perkakas'      => 'tool',
            'pompa'         => 'tool',

            // Elektronik & Komputer
            'komputer'      => 'monitor',
            'laptop'        => 'monitor',
            'elektronik'    => 'monitor',
            'printer'       => 'printer',
            'kamera'        => 'camera',
            'proyektor'     => 'monitor',

            // Listrik & Energi
            'listrik'       => 'zap',
            'genset'        => 'zap',
            'panel'         => 'zap',
            'energi'        => 'zap',

            // Furnitur & Perlengkapan Kantor
            'furnitur'      => 'sidebar',
            'furniture'     => 'sidebar',
            'meja'          => 'sidebar',
            'kursi'         => 'sidebar',
            'lemari'        => 'sidebar',

            // Buku, Dokumen & Arsip
            'buku'          => 'book-open',
            'arsip'         => 'archive',
            'dokumen'       => 'file-text',
            'surat'         => 'file-text',

            // Tidak ditemukan / Hilang (LHI)
            'tidak diketemukan' => 'alert-triangle',
            'tidak ditemukan'   => 'alert-triangle',
            'hilang'        => 'alert-triangle',
            'lhi'           => 'alert-triangle',
            'rusak'         => 'alert-triangle',

            // Aset Desa / Umum
            'aset'          => 'layers',
            'inventaris'    => 'layers',
            'tetap'         => 'layers',
            'lainnya'       => 'package',
        ];

        foreach ($keywordMap as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }

        return 'layers'; // fallback universal
    }

    /**
     * Pool warna gradient vibrant untuk semua jenis inventaris (deterministik via hash).
     * Tidak ada putih — semua warna memiliki kontras tinggi dengan icon putih.
     */
    private static function getColorPool(): array
    {
        return [
            ['#3f4d67', '#2d3a4f'], // navy (tema sistem)
            ['#6366f1', '#4f46e5'], // indigo
            ['#0ea5e9', '#0284c7'], // sky
            ['#14b8a6', '#0d9488'], // teal
            ['#10b981', '#059669'], // emerald
            ['#84cc16', '#65a30d'], // lime
            ['#f59e0b', '#d97706'], // amber
            ['#f97316', '#ea580c'], // orange
            ['#ef4444', '#dc2626'], // red
            ['#f43f5e', '#e11d48'], // rose
            ['#ec4899', '#db2777'], // pink
            ['#a855f7', '#9333ea'], // purple
            ['#8b5cf6', '#7c3aed'], // violet
            ['#06b6d4', '#0891b2'], // cyan
        ];
    }

    /**
     * Accessor: mendapatkan icon berdasarkan kata kunci nama.
     * Jika icon_mode = 'manual' dan custom_icon diisi, gunakan custom_icon.
     * Jika icon_mode = 'auto', generate dari keyword nama.
     */
    public function getIconAttribute(): string
    {
        if (($this->icon_mode ?? 'auto') === 'manual' && !empty($this->custom_icon)) {
            return $this->custom_icon;
        }

        return self::resolveIconByKeywords($this->nama);
    }

    /**
     * Accessor: mendapatkan warna gradient secara deterministik berdasarkan nama.
     * Jika icon_mode = 'manual' dan custom_color diisi, gunakan custom_color (solid).
     * Jika icon_mode = 'auto', generate dari color pool.
     * Return format: ['#color1', '#color2']
     */
    public function getColorGradientAttribute(): array
    {
        if (($this->icon_mode ?? 'auto') === 'manual' && !empty($this->custom_color)) {
            // Buat gradient dari warna yang sama (sedikit lebih gelap untuk color2)
            $hex = ltrim($this->custom_color, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            // Darken by ~15%
            $r2 = max(0, (int) round($r * 0.85));
            $g2 = max(0, (int) round($g * 0.85));
            $b2 = max(0, (int) round($b * 0.85));
            $color2 = sprintf('#%02x%02x%02x', $r2, $g2, $b2);
            return [$this->custom_color, $color2];
        }

        $pool  = self::getColorPool();
        $index = abs(crc32($this->nama)) % count($pool);
        return $pool[$index];
    }

    /**
     * Helper method untuk mendapatkan gradient CSS string
     */
    public function getColorGradientCss()
    {
        $colors = $this->color_gradient;
        return "linear-gradient(135deg, {$colors[0]} 0%, {$colors[1]} 100%)";
    }

    /**
     * Check apakah jenis ini virtual (aggregated dari semua jenis lain)
     * Virtual jenis: Buku Inventaris Aset Desa - tidak punya fields sendiri, 
     * hanya aggregation dari semua real jenis
     */
    public function isVirtual(): bool
    {
        return $this->nama === 'Buku Inventaris Aset Desa';
    }

    /**
     * Nama resmi jenis template LHI (barang tidak ditemukan).
     */
    public const NAMA_TIDAK_DITEMUKAN = 'Inventaris yang tidak ditemukan';

    /**
     * Check apakah jenis ini adalah template LHI "Inventaris yang tidak ditemukan".
     * Jenis ini punya field pakem yang terkunci (tidak bisa tambah/hapus/ubah).
     * User hanya bisa: deskripsi, icon, pejabat penandatangan, urutan & wajib field.
     * Tidak bisa: ubah nama, requires_ruangan, dropdown, tambah field custom.
     */
    public function isTidakDitemukan(): bool
    {
        return $this->nama === self::NAMA_TIDAK_DITEMUKAN;
    }

    /**
     * Get jenis inventaris untuk aggregation (semua kecuali Buku Inventaris)
     * Digunakan untuk query inventaris ketika export Buku Inventaris
     */
    public function getAggregatedJenis()
    {
        if (!$this->isVirtual()) {
            return collect([$this]);
        }
        
        // Return semua jenis KECUALI Buku Inventaris
        return static::where('nama', '!=', 'Buku Inventaris Aset Desa')
                    ->orderBy('nama')
                    ->get();
    }
}
