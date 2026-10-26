<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Desa — digunakan pada header dokumen export
    |--------------------------------------------------------------------------
    | Atur nilai ini di .env. Contoh untuk Desa Demo:
    |
    |   KODE_LOKASI_DESA=111.3.2.2009
    |   NAMA_LOKASI_DESA=DEMO
    |   DESA_PROVINSI_KODE=111
    |   DESA_PROVINSI_NAMA=BALI
    |   DESA_KABUPATEN_KODE=111.3
    |   DESA_KABUPATEN_NAMA=KABUPATEN DEMO
    |   DESA_KECAMATAN_KODE=111.3.2
    |   DESA_KECAMATAN_NAMA=KECAMATAN DEMO
    |   DESA_NAMA=DEMO
    |   DESA_UPB=PEMERINTAH DESA DEMO
    */

    // Kode & nama lokasi (dipakai di Buku Inventaris Aset Desa)
    'kode_lokasi' => env('KODE_LOKASI_DESA', ''),
    'nama_lokasi' => env('NAMA_LOKASI_DESA', ''),

    // Identitas lengkap untuk header Kartu Inventaris Ruangan & dokumen resmi
    'provinsi_kode'   => env('DESA_PROVINSI_KODE', ''),
    'provinsi_nama'   => env('DESA_PROVINSI_NAMA', ''),
    'kabupaten_kode'  => env('DESA_KABUPATEN_KODE', ''),
    'kabupaten_nama'  => env('DESA_KABUPATEN_NAMA', ''),
    'kecamatan_kode'  => env('DESA_KECAMATAN_KODE', ''),
    'kecamatan_nama'  => env('DESA_KECAMATAN_NAMA', ''),
    'desa_nama'       => env('DESA_NAMA', ''),
    'upb'             => env('DESA_UPB', ''),

    // Otorisasi Super Developer / Platform Owner (pemilik sistem SaaS)
    // Hanya akun-akun ini yang dapat mengakses Cadangan Basis Data (Backup)
    'super_developer_usernames' => env('SUPER_DEVELOPER_USERNAMES', 'superdeveloper,developer,owner'),
    'super_developer_emails'    => env('SUPER_DEVELOPER_EMAILS', ''),
];

