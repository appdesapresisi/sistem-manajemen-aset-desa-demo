<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi KIB (Kartu Inventaris Barang)
|--------------------------------------------------------------------------
|
| Mendefinisikan field statis per KIB sesuai format standar Permendagri.
|
| Struktur setiap field:
|   'key'        => nama kolom di inventaris_kib_values.kib_field_key
|   'label'      => label tampilan di form input aset
|   'type'       => text | textarea | date | year | decimal | select
|   'options'    => array pilihan (hanya untuk type=select)
|   'required'   => apakah wajib diisi (bool)
|   'export_col' => nama kolom di header export KIB
|   'export_group' => grup kolom di header (untuk sub-kolom, opsional)
|
| Untuk pengembangan ke depan (fleksibel):
|   Pindahkan definisi ini ke tabel DB `kib_field_definitions` dan buat
|   UI master data. Tabel `inventaris_kib_values` tidak perlu diubah.
|   Lihat README-KIB.md untuk panduan lengkap.
|
*/

return [

    // =========================================================================
    // KIB A — TANAH
    // =========================================================================
    'A' => [
        'nama'          => 'Tanah',
        'judul_export'  => 'KARTU INVENTARIS BARANG TANAH (KIB A)',
        'fields'        => [
            [
                'key'         => 'luas_m2',
                'label'       => 'Luas (M2)',
                'type'        => 'decimal',
                'required'    => false,
                'export_col'  => 'LUAS (M2)',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'TAHUN BUKU',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'alamat',
                'label'       => 'Alamat',
                'type'        => 'textarea',
                'required'    => false,
                'export_col'  => 'ALAMAT',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'hak',
                'label'       => 'Hak',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'HAK',
                'export_group'=> 'STATUS TANAH',
            ],
            [
                'key'         => 'sertifikat_nomor',
                'label'       => 'Nomor Sertifikat',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'NOMOR',
                'export_group'=> 'SERTIFIKAT',
            ],
            [
                'key'         => 'sertifikat_tgl',
                'label'       => 'Tanggal Sertifikat',
                'type'        => 'date',
                'required'    => false,
                'export_col'  => 'TGL',
                'export_group'=> 'SERTIFIKAT',
            ],
        ],
    ],

    // =========================================================================
    // KIB B — PERALATAN DAN MESIN
    // =========================================================================
    'B' => [
        'nama'          => 'Peralatan dan Mesin',
        'judul_export'  => 'KARTU INVENTARIS BARANG PERALATAN DAN MESIN (KIB B)',
        'fields'        => [
            [
                'key'         => 'merk_type',
                'label'       => 'Merk / Type',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'MERK/TYPE',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'ukuran_cc',
                'label'       => 'Ukuran / CC',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'UKURAN/AN/CC',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'bahan',
                'label'       => 'Bahan',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'SAHAN',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'BUKU',
                'export_group'=> 'TAHUN',
            ],
            [
                'key'         => 'no_pabrik',
                'label'       => 'Nomor Pabrik',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'PABRIK',
                'export_group'=> 'NOMOR',
            ],
            [
                'key'         => 'no_rangka',
                'label'       => 'Nomor Rangka',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'RANGKA',
                'export_group'=> 'NOMOR',
            ],
            [
                'key'         => 'no_mesin',
                'label'       => 'Nomor Mesin',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'MESIN',
                'export_group'=> 'NOMOR',
            ],
            [
                'key'         => 'no_polisi',
                'label'       => 'Nomor Polisi',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'POLISI',
                'export_group'=> 'NOMOR',
            ],
            [
                'key'         => 'no_bpkb',
                'label'       => 'Nomor BPKB',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'BPKB',
                'export_group'=> 'NOMOR',
            ],
        ],
    ],

    // =========================================================================
    // KIB C — GEDUNG DAN BANGUNAN
    // =========================================================================
    'C' => [
        'nama'          => 'Gedung dan Bangunan',
        'judul_export'  => 'KARTU INVENTARIS BARANG GEDUNG DAN BANGUNAN (KIB C)',
        'fields'        => [
            [
                'key'         => 'konstruksi_bertingkat',
                'label'       => 'Konstruksi: Bertingkat/Tidak',
                'type'        => 'select',
                'options'     => ['BERTINGKAT', 'TIDAK BERTINGKAT'],
                'required'    => false,
                'export_col'  => 'BERTINGKAT AT/TIDAK',
                'export_group'=> 'KONSTRUKSI BANGUNAN',
            ],
            [
                'key'         => 'konstruksi_beton',
                'label'       => 'Konstruksi: Beton/Tidak',
                'type'        => 'select',
                'options'     => ['BETON', 'TIDAK BETON'],
                'required'    => false,
                'export_col'  => 'BETON/TIDAK',
                'export_group'=> 'KONSTRUKSI BANGUNAN',
            ],
            [
                'key'         => 'luas_m2',
                'label'       => 'Luas (M2)',
                'type'        => 'decimal',
                'required'    => false,
                'export_col'  => 'LUAS (M2)',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'BUKU',
                'export_group'=> 'TAHUN',
            ],
            [
                'key'         => 'dokumen_tgl',
                'label'       => 'Tanggal Dokumen Gedung',
                'type'        => 'date',
                'required'    => false,
                'export_col'  => 'TANGGAL',
                'export_group'=> 'DOKUMEN GEDUNG',
            ],
            [
                'key'         => 'dokumen_nomor',
                'label'       => 'Nomor Dokumen Gedung',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'NOMOR',
                'export_group'=> 'DOKUMEN GEDUNG',
            ],
            [
                'key'         => 'alamat',
                'label'       => 'Alamat',
                'type'        => 'textarea',
                'required'    => false,
                'export_col'  => 'ALAMAT',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'status_tanah',
                'label'       => 'Status Tanah',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'STATUS TANAH',
                'export_group'=> null,
            ],
            [
                'key'         => 'nomor_kode_tanah',
                'label'       => 'Nomor Kode Tanah',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'NOMOR KODE TANAH',
                'export_group'=> null,
            ],
        ],
    ],

    // =========================================================================
    // KIB D — JALAN, IRIGASI DAN JARINGAN
    // =========================================================================
    'D' => [
        'nama'          => 'Jalan, Irigasi dan Jaringan',
        'judul_export'  => 'KARTU INVENTARIS BARANG JALAN, IRIGASI DAN JARINGAN (KIB D)',
        'fields'        => [
            [
                'key'         => 'konstruksi',
                'label'       => 'Konstruksi',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'KONSTRUKSI',
                'export_group'=> null,
            ],
            [
                'key'         => 'panjang_km',
                'label'       => 'Panjang (KM)',
                'type'        => 'decimal',
                'required'    => false,
                'export_col'  => 'PANJANG (KM)',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'lebar_m',
                'label'       => 'Lebar (M)',
                'type'        => 'decimal',
                'required'    => false,
                'export_col'  => 'LEBAR (M)',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'luas_m2',
                'label'       => 'Luas (M2)',
                'type'        => 'decimal',
                'required'    => false,
                'export_col'  => 'LUAS (M2)',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'BUKU',
                'export_group'=> 'TAHUN',
            ],
            [
                'key'         => 'dokumen_tgl',
                'label'       => 'Tanggal Dokumen',
                'type'        => 'date',
                'required'    => false,
                'export_col'  => 'TGL',
                'export_group'=> 'DOKUMEN',
            ],
            [
                'key'         => 'dokumen_nomor',
                'label'       => 'Nomor Dokumen',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'NOMOR',
                'export_group'=> 'DOKUMEN',
            ],
            [
                'key'         => 'alamat',
                'label'       => 'Alamat / Lokasi',
                'type'        => 'textarea',
                'required'    => false,
                'export_col'  => 'ALAMAT',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'tanah_status',
                'label'       => 'Status Tanah',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'STATUS',
                'export_group'=> 'TANAH',
            ],
            [
                'key'         => 'tanah_kode',
                'label'       => 'Kode Tanah',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'KODE',
                'export_group'=> 'TANAH',
            ],
        ],
    ],

    // =========================================================================
    // KIB E — ASET TETAP LAINNYA
    // =========================================================================
    'E' => [
        'nama'          => 'Aset Tetap Lainnya',
        'judul_export'  => 'KARTU INVENTARIS BARANG ASET TETAP LAINNYA (KIB E)',
        'fields'        => [
            [
                'key'         => 'judul_pencipta',
                'label'       => 'Judul / Pencipta',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'JUDUL/PENCIPTA',
                'export_group'=> 'BUKU/PERPUSTAKAAN',
            ],
            [
                'key'         => 'spesifikasi',
                'label'       => 'Spesifikasi',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'SPESIFI-KASI',
                'export_group'=> 'BUKU/PERPUSTAKAAN',
            ],
            [
                'key'         => 'asal_daerah',
                'label'       => 'Asal Daerah',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'ASAL DAERAH',
                'export_group'=> 'BARANG BERCORAK KESENIAN/KEBUDAYAAN',
            ],
            [
                'key'         => 'pencipta_kesenian',
                'label'       => 'Pencipta',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'PENCIPTA',
                'export_group'=> 'BARANG BERCORAK KESENIAN/KEBUDAYAAN',
            ],
            [
                'key'         => 'bahan',
                'label'       => 'Bahan',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'BAHAN',
                'export_group'=> 'BARANG BERCORAK KESENIAN/KEBUDAYAAN',
            ],
            [
                'key'         => 'jenis_hewan',
                'label'       => 'Jenis',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'JENIS',
                'export_group'=> 'HEWAN/TERNAK DAN TUMBUHAN',
            ],
            [
                'key'         => 'ukuran',
                'label'       => 'Ukuran',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'UKURAN',
                'export_group'=> 'HEWAN/TERNAK DAN TUMBUHAN',
            ],
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'BUKU',
                'export_group'=> 'TAHUN PEROLEHAN/BUKU',
            ],
        ],
    ],

    // =========================================================================
    // KIB F — KONSTRUKSI DALAM PENGERJAAN
    // =========================================================================
    'F' => [
        'nama'          => 'Konstruksi Dalam Pengerjaan',
        'judul_export'  => 'KARTU INVENTARIS BARANG KONSTRUKSI DALAM PENGERJAAN (KIB F)',
        'fields'        => [
            [
                'key'         => 'tahun_buku',
                'label'       => 'Tahun Buku',
                'type'        => 'year',
                'required'    => false,
                'export_col'  => 'TAHUN BUKU',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'dokumen_tgl',
                'label'       => 'Tanggal Dokumen',
                'type'        => 'date',
                'required'    => false,
                'export_col'  => 'TGL DOKUMEN',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'dokumen_nomor',
                'label'       => 'Nomor Dokumen',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'NO DOKUMEN',
                'export_group'=> 'IDENTITAS',
            ],
            [
                'key'         => 'bidang',
                'label'       => 'Bidang',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'BIDANG',
                'export_group'=> null,
            ],
            [
                'key'         => 'sumber_dana',
                'label'       => 'Sumber Dana',
                'type'        => 'text',
                'required'    => false,
                'export_col'  => 'SUMBER DANA',
                'export_group'=> null,
            ],
        ],
    ],

];
