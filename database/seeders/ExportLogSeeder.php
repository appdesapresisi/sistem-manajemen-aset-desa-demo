<?php

namespace Database\Seeders;

use App\Models\ExportLog;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ExportLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi 10 data dummy riwayat ekspor & audit sesuai struktur database dan fitur SIMA.
     */
    public function run(): void
    {
        ExportLog::truncate();

        // 1. Resolve Users dari database
        $superAdmin = User::where('username', 'superadmin')->first() ?? User::first();
        $sekdes     = User::where('username', 'sekdes')->first() ?? $superAdmin;
        $kaurKeu    = User::where('username', 'kaurkeu')->first() ?? $superAdmin;
        $kaurPlan   = User::where('username', 'kaurplan')->first() ?? $superAdmin;
        $kasiPem    = User::where('username', 'kasipem')->first() ?? $superAdmin;
        $kasiPel    = User::where('username', 'kasipelayanan')->first() ?? $superAdmin;
        $stafPem    = User::where('username', 'stafpem')->first() ?? $superAdmin;

        // 2. Resolve Jenis Inventaris dari database
        $jenisTanah      = JenisInventaris::where('kib_type', 'A')->first();
        $jenisMesin      = JenisInventaris::where('kib_type', 'B')->where('nama', 'like', '%Mesin%')->first()
                           ?? JenisInventaris::where('kib_type', 'B')->first();
        $jenisKendaraan  = JenisInventaris::where('kib_type', 'B')->where('nama', 'like', '%Kendaraan%')->first()
                           ?? JenisInventaris::where('kib_type', 'B')->first();
        $jenisBangunan   = JenisInventaris::where('kib_type', 'C')->first();
        $jenisJalan      = JenisInventaris::where('kib_type', 'D')->first();
        $jenisAsetLain   = JenisInventaris::where('kib_type', 'E')->first();

        // 3. Resolve Pejabat dari database
        $pejabatPerbekel = Pejabat::where('jabatan', 'like', '%Perbekel%')->first();
        $pejabatSekdes   = Pejabat::where('jabatan', 'like', '%Sekretaris%')->first();
        $pejabatKaurKeu  = Pejabat::where('jabatan', 'like', '%Keuangan%')->first();
        $pejabatKaurPlan = Pejabat::where('jabatan', 'like', '%Perencanaan%')->first();

        $namaPerbekel = $pejabatPerbekel?->nama ?? 'I Wayan Pratama';
        $namaSekdes   = $pejabatSekdes?->nama ?? 'Ni Made Lestari';
        $namaKaurKeu  = $pejabatKaurKeu?->nama ?? 'I Ketut Wijaya';
        $namaKaurPlan = $pejabatKaurPlan?->nama ?? 'Ni Nyoman Sri';

        // 4. Definisikan 10 data dummy realistis sesuai fitur ekspor SIMA
        $dummyLogs = [
            // 1. Ekspor KIB B (Peralatan & Mesin) - Excel (1 jam lalu)
            [
                'user_id'             => $superAdmin?->id,
                'jenis_inventaris_id' => $jenisMesin?->id,
                'pejabat_id'          => $pejabatSekdes?->id,
                'tipe_export'         => 'excel',
                'nama_file'           => 'kib_b_peralatan_mesin_' . now()->subHours(1)->format('Ymd_His') . '.xlsx',
                'pejabat_override'    => [
                    ['nama' => $namaSekdes, 'jabatan' => 'Sekretaris Desa'],
                ],
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'B',
                    'tahun'            => 'all',
                    'signature_option' => 'single',
                ],
                'created_at'          => now()->subHours(1),
            ],

            // 2. Ekspor KIB B (Kendaraan Dinas) - PDF (3 jam lalu)
            [
                'user_id'             => $sekdes?->id,
                'jenis_inventaris_id' => $jenisKendaraan?->id,
                'pejabat_id'          => $pejabatPerbekel?->id,
                'tipe_export'         => 'pdf',
                'nama_file'           => 'kib_b_kendaraan_operasional_' . now()->subHours(3)->format('Ymd_His') . '.pdf',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                    ['nama' => $namaSekdes, 'jabatan' => 'Sekretaris Desa'],
                ],
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'B',
                    'tahun'            => (string) (now()->year - 1),
                    'signature_option' => 'with_pejabat',
                ],
                'created_at'          => now()->subHours(3),
            ],

            // 3. Ekspor Buku Inventaris Aset Desa (KIB A-F Lengkap) - Excel (Kemarin)
            [
                'user_id'             => $kaurKeu?->id,
                'jenis_inventaris_id' => null,
                'pejabat_id'          => $pejabatPerbekel?->id,
                'tipe_export'         => 'excel',
                'nama_file'           => 'buku_inventaris_aset_desa_tahunan_' . now()->year . '_' . now()->subDays(1)->format('Ymd_His') . '.xlsx',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                    ['nama' => $namaKaurKeu, 'jabatan' => 'Kaur Keuangan'],
                ],
                'filters'             => [
                    'virtual_export_as' => 'Buku Inventaris Aset Desa',
                    'mode'              => 'buku_inventaris_aset',
                    'periode'           => 'tahunan',
                    'tahun'             => (string) now()->year,
                    'signature_option'  => 'with_pejabat',
                ],
                'created_at'          => now()->subDays(1),
            ],

            // 4. Ekspor KIB C (Gedung & Bangunan) - PDF (2 hari lalu)
            [
                'user_id'             => $superAdmin?->id,
                'jenis_inventaris_id' => $jenisBangunan?->id,
                'pejabat_id'          => $pejabatSekdes?->id,
                'tipe_export'         => 'pdf',
                'nama_file'           => 'kib_c_gedung_bangunan_' . now()->subDays(2)->format('Ymd_His') . '.pdf',
                'pejabat_override'    => [
                    ['nama' => $namaSekdes, 'jabatan' => 'Sekretaris Desa'],
                ],
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'C',
                    'tahun'            => 'all',
                    'signature_option' => 'single',
                ],
                'created_at'          => now()->subDays(2),
            ],

            // 5. Ekspor KIB A (Tanah Kas Desa) - Excel (3 hari lalu)
            [
                'user_id'             => $stafPem?->id,
                'jenis_inventaris_id' => $jenisTanah?->id,
                'pejabat_id'          => $pejabatPerbekel?->id,
                'tipe_export'         => 'excel',
                'nama_file'           => 'kib_a_tanah_kas_desa_' . now()->subDays(3)->format('Ymd_His') . '.xlsx',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                ],
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'A',
                    'tahun'            => 'all',
                    'signature_option' => 'single',
                ],
                'created_at'          => now()->subDays(3),
            ],

            // 6. Ekspor Kartu Inventaris Ruangan (KIR Format Khusus 3 TTD) - PDF (5 hari lalu)
            [
                'user_id'             => $kasiPel?->id,
                'jenis_inventaris_id' => null,
                'pejabat_id'          => null,
                'tipe_export'         => 'pdf',
                'nama_file'           => 'kir_ruang_pelayanan_demo_' . now()->subDays(5)->format('Ymd_His') . '.pdf',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                    ['nama' => $kasiPel?->nama ?? 'Ni Putu Ayu', 'jabatan' => 'Penanggung Jawab Ruangan'],
                    ['nama' => $namaSekdes, 'jabatan' => 'Pengurus Barang'],
                ],
                'filters'             => [
                    'virtual_export_as' => 'KIR - Ruang Pelayanan Umum',
                    'mode'              => 'ruangan',
                    'ruangan_id'        => 1,
                    'template'          => 'format2',
                    'signature_option'  => 'custom',
                ],
                'created_at'          => now()->subDays(5),
            ],

            // 7. Ekspor KIB D (Jalan, Irigasi & Jaringan - Audit Tanpa TTD) - Excel (7 hari lalu)
            [
                'user_id'             => $kasiPem?->id,
                'jenis_inventaris_id' => $jenisJalan?->id,
                'pejabat_id'          => null,
                'tipe_export'         => 'excel',
                'nama_file'           => 'kib_d_jalan_irigasi_audit_' . now()->subDays(7)->format('Ymd_His') . '.xlsx',
                'pejabat_override'    => null,
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'D',
                    'tahun'            => 'all',
                    'signature_option' => 'without',
                ],
                'created_at'          => now()->subDays(7),
            ],

            // 8. Ekspor Laporan Mutasi Aset (Semester I) - PDF (10 hari lalu)
            [
                'user_id'             => $kaurKeu?->id,
                'jenis_inventaris_id' => null,
                'pejabat_id'          => $pejabatPerbekel?->id,
                'tipe_export'         => 'pdf',
                'nama_file'           => 'laporan_mutasi_aset_semester_1_' . now()->subDays(10)->format('Ymd_His') . '.pdf',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                    ['nama' => $namaKaurKeu, 'jabatan' => 'Kaur Keuangan'],
                ],
                'filters'             => [
                    'virtual_export_as' => 'Laporan Mutasi Aset (Semester I)',
                    'mode'              => 'laporan',
                    'periode'           => 'semester_1',
                    'tahun'             => (string) now()->year,
                    'signature_option'  => 'with_pejabat',
                ],
                'created_at'          => now()->subDays(10),
            ],

            // 9. Ekspor KIB E (Aset Tetap Lainnya) - Excel (12 hari lalu)
            [
                'user_id'             => $kaurPlan?->id,
                'jenis_inventaris_id' => $jenisAsetLain?->id,
                'pejabat_id'          => $pejabatKaurPlan?->id,
                'tipe_export'         => 'excel',
                'nama_file'           => 'kib_e_buku_dan_kesenian_' . now()->subDays(12)->format('Ymd_His') . '.xlsx',
                'pejabat_override'    => [
                    ['nama' => $namaKaurPlan, 'jabatan' => 'Kaur Perencanaan'],
                ],
                'filters'             => [
                    'mode'             => 'kib',
                    'kib_type'         => 'E',
                    'tahun'            => 'all',
                    'signature_option' => 'single',
                ],
                'created_at'          => now()->subDays(12),
            ],

            // 10. Ekspor Rekapitulasi Semua Jenis Aset - PDF (20 hari lalu)
            [
                'user_id'             => $superAdmin?->id,
                'jenis_inventaris_id' => null,
                'pejabat_id'          => $pejabatPerbekel?->id,
                'tipe_export'         => 'pdf',
                'nama_file'           => 'rekapitulasi_aset_desa_lengkap_' . now()->subDays(20)->format('Ymd_His') . '.pdf',
                'pejabat_override'    => [
                    ['nama' => $namaPerbekel, 'jabatan' => 'Perbekel'],
                    ['nama' => $namaSekdes, 'jabatan' => 'Sekretaris Desa'],
                ],
                'filters'             => [
                    'virtual_export_as' => 'Semua Kategori Aset',
                    'mode'              => 'all_jenis',
                    'tahun'             => 'all',
                    'signature_option'  => 'with_pejabat',
                ],
                'created_at'          => now()->subDays(20),
            ],
        ];

        foreach ($dummyLogs as $logData) {
            $createdTime = $logData['created_at'];
            unset($logData['created_at']);

            $log = ExportLog::create($logData);
            $log->created_at = $createdTime;
            $log->updated_at = $createdTime;
            $log->save();

            // Opsional: Buat file dummy kosong di storage agar file audit dapat diunduh jika diakses
            try {
                $dir = 'exports/' . $createdTime->format('Y/m');
                Storage::disk('local')->makeDirectory($dir);
                Storage::disk('local')->put($dir . '/' . $log->nama_file, 'SIMA Audit File Dummy Content: ' . $log->nama_file);
            } catch (\Throwable $e) {
                // Ignore storage write issues in restricted environments
            }
        }
    }
}
