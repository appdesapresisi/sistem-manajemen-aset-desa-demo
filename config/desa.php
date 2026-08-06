<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Desa — digunakan pada header dokumen export
    |--------------------------------------------------------------------------
    | Atur nilai ini di .env. Contoh untuk Desa Demo:
    |
    |   KODE_LOKASI_DESA=51.3.2.2009
    |   NAMA_LOKASI_DESA=Mengwi
    |   DESA_PROVINSI_KODE=51
    |   DESA_PROVINSI_NAMA=BALI
    |   DESA_KABUPATEN_KODE=51.3
    |   DESA_KABUPATEN_NAMA=KABUPATEN BADUNG
    |   DESA_KECAMATAN_KODE=51.3.2
    |   DESA_KECAMATAN_NAMA=KECAMATAN MENGWI
    |   DESA_NAMA=MENGWI
    |   DESA_UPB=PEMERINTAH DESA MENGWI
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
];
