<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Inventaris;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi log aktivitas realistis sesuai tata kelola penatausahaan aset Desa Percontohan (Demo) (Permendagri No. 1/2016).
     */
    public function run(): void
    {
        // Bersihkan data log lama
        ActivityLog::truncate();

        // Resolusi user riil di database sesuai hierarki tata usaha desa
        $admin = User::where('username', 'superadmin')->orWhere('id', 1)->first() ?? User::first();
        $sekdes = User::where('username', 'sekdes')->first() ?? $admin;
        $kaurKeu = User::where('username', 'kaurkeu')->first() ?? $admin;
        $kaurPlan = User::where('username', 'kaurplan')->first() ?? $admin;
        $kasiPem = User::where('username', 'kasipem')->first() ?? $admin;
        $kasiKesra = User::where('username', 'kasikesra')->first() ?? $admin;
        $kasiPelayanan = User::where('username', 'kasipelayanan')->first() ?? $admin;
        $staf = User::where('username', 'stafpem')->first() ?? $admin;
        $perbekel = User::where('username', 'perbekel')->first() ?? $admin;

        // Referensi aset riil dari database (jika ada)
        $asetTanah = Inventaris::where('kode_barang', 'like', '1.01%')->first() ?? Inventaris::find(59);
        $asetMejaKerja = Inventaris::where('nama_barang', 'like', '%Meja Kerja%')->first() ?? Inventaris::find(101);
        $asetBrankas = Inventaris::where('nama_barang', 'like', '%Brankas%')->first() ?? Inventaris::find(110);
        $asetRak = Inventaris::where('nama_barang', 'like', '%Rak%')->first() ?? Inventaris::find(100);
        $asetLemariArsip = Inventaris::where('nama_barang', 'like', '%Lemari Arsip%')->first() ?? Inventaris::find(106);
        $asetKursiTamu = Inventaris::where('nama_barang', 'like', '%Kursi Tamu%')->first() ?? Inventaris::find(103);
        $asetFilling = Inventaris::where('nama_barang', 'like', '%Filling%')->first() ?? Inventaris::find(111);
        $asetLemariEs = Inventaris::where('nama_barang', 'like', '%Lemari Es%')->first() ?? Inventaris::find(115);

        // Referensi ruangan riil dari database
        $ruangPelayanan = Ruangan::where('nama', 'like', '%Pelayanan%')->first() ?? Ruangan::find(3);
        $ruangSekdes = Ruangan::where('nama', 'like', '%Sekretaris%')->first() ?? Ruangan::find(2);
        $ruangKaurKasi = Ruangan::where('nama', 'like', '%Kaur%')->first() ?? Ruangan::find(4);
        $ruangPerpus = Ruangan::where('nama', 'like', '%Perpustakaan%')->first() ?? Ruangan::find(9);
        $ruangPerbekel = Ruangan::where('nama', 'like', '%Perbekel%')->first() ?? Ruangan::find(1);
        $aulaDesa = Ruangan::where('nama', 'like', '%Aula%')->first() ?? Ruangan::find(5);

        $now = Carbon::now();

        $logs = [
            // 1. Hari ini - 18 menit lalu (Pusat Ekspor)
            [
                'user_id'      => $sekdes?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Buku Inventaris (KIB B) format Excel (.xlsx)',
                'subject_id'   => null,
                'subject_name' => 'Buku Inventaris (KIB B - Peralatan dan Mesin)',
                'created_at'   => (clone $now)->subMinutes(18),
            ],
            // 2. Hari ini - 42 menit lalu (Pemutakhiran kondisi aset)
            [
                'user_id'      => $staf?->id ?? $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'ubah',
                'detail'       => 'mengubah kondisi inventaris ' . ($asetMejaKerja?->kode_barang ?? '3.05.02.01.002') . ' - ' . ($asetMejaKerja?->nama_barang ?? 'Meja Kerja (3)') . ' menjadi Rusak Ringan',
                'subject_id'   => $asetMejaKerja?->id ?? 101,
                'subject_name' => $asetMejaKerja?->nama_barang ?? 'Meja Kerja (3)',
                'created_at'   => (clone $now)->subMinutes(42),
            ],
            // 3. Hari ini - 2 jam lalu (Penambahan inventaris baru)
            [
                'user_id'      => $kaurKeu?->id ?? $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'tambah',
                'detail'       => 'menambahkan inventaris ' . ($asetBrankas?->kode_barang ?? '3.05.01.04.007') . ' - ' . ($asetBrankas?->nama_barang ?? 'Brankas') . ' penempatan ' . ($ruangSekdes?->nama ?? 'Ruang Sekretaris Desa'),
                'subject_id'   => $asetBrankas?->id ?? 110,
                'subject_name' => $asetBrankas?->nama_barang ?? 'Brankas',
                'created_at'   => (clone $now)->subHours(2)->subMinutes(14),
            ],
            // 4. Hari ini - 3 jam lalu (Ekspor format khusus)
            [
                'user_id'      => $kaurPlan?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Format Jenis Barang kategori Peralatan dan Mesin format Excel',
                'subject_id'   => null,
                'subject_name' => 'Format Jenis Barang (Peralatan dan Mesin)',
                'created_at'   => (clone $now)->subHours(3)->subMinutes(35),
            ],
            // 5. Hari ini - 5 jam lalu (Pemindahan inventaris antar ruangan)
            [
                'user_id'      => $kasiPem?->id ?? $admin->id,
                'module'       => 'Ruangan',
                'action'       => 'ubah',
                'detail'       => 'mengubah penempatan inventaris ' . ($asetLemariArsip?->kode_barang ?? '3.05.01.04.002') . ' - ' . ($asetLemariArsip?->nama_barang ?? 'Lemari Arsip') . ' ke ' . ($ruangKaurKasi?->nama ?? 'Ruang Staf Kaur & Kasi'),
                'subject_id'   => $asetLemariArsip?->id ?? 106,
                'subject_name' => $asetLemariArsip?->nama_barang ?? 'Lemari Arsip',
                'created_at'   => (clone $now)->subHours(5)->subMinutes(10),
            ],
            // 6. Kemarin (Ekspor Buku Inventaris Desa Lengkap)
            [
                'user_id'      => $sekdes?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Buku Inventaris Desa lengkap (KIB A s/d F) format Excel (.xlsx)',
                'subject_id'   => null,
                'subject_name' => 'Buku Inventaris Desa Lengkap',
                'created_at'   => (clone $now)->subDay()->subHours(2),
            ],
            // 7. Kemarin (Ekspor KIR oleh Perbekel)
            [
                'user_id'      => $perbekel?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Kartu Inventaris Ruangan (KIR) ' . ($ruangPerbekel?->nama ?? 'Ruang Perbekel') . ' format PDF',
                'subject_id'   => $ruangPerbekel?->id ?? 1,
                'subject_name' => 'KIR ' . ($ruangPerbekel?->nama ?? 'Ruang Perbekel'),
                'created_at'   => (clone $now)->subDay()->subHours(5),
            ],
            // 8. Kemarin (Penambahan fasilitas perpustakaan desa)
            [
                'user_id'      => $kasiKesra?->id ?? $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'tambah',
                'detail'       => 'menambahkan inventaris ' . ($asetRak?->kode_barang ?? '3.05.01.04.004') . ' - ' . ($asetRak?->nama_barang ?? 'Rak Perpustakaan') . ' di ' . ($ruangPerpus?->nama ?? 'Perpustakaan Desa'),
                'subject_id'   => $asetRak?->id ?? 100,
                'subject_name' => $asetRak?->nama_barang ?? 'Rak Perpustakaan',
                'created_at'   => (clone $now)->subDay()->subHours(7),
            ],
            // 9. 2 Hari lalu (Pengajuan berkas usulan penghapusan)
            [
                'user_id'      => $sekdes?->id ?? $admin->id,
                'module'       => 'Penghapusan Aset',
                'action'       => 'hapus',
                'detail'       => 'mengajukan usulan penghapusan 2 unit inventaris kondisi Rusak Berat (SK: 042/BA-AS/2026)',
                'subject_id'   => $asetLemariEs?->id ?? 115,
                'subject_name' => 'Usulan Penghapusan SK No. 042/BA-AS/2026',
                'created_at'   => (clone $now)->subDays(2)->subHours(3),
            ],
            // 10. 2 Hari lalu (Pencatatan aset tanah desa)
            [
                'user_id'      => $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'tambah',
                'detail'       => 'menambahkan inventaris ' . ($asetTanah?->kode_barang ?? '1.01.01.01.001') . ' - ' . ($asetTanah?->nama_barang ?? 'Tanah Kantor Desa') . ' (KIB A - Tanah Hak Pakai)',
                'subject_id'   => $asetTanah?->id ?? 59,
                'subject_name' => $asetTanah?->nama_barang ?? 'Tanah Kantor Desa',
                'created_at'   => (clone $now)->subDays(2)->subHours(6),
            ],
            // 11. 3 Hari lalu (Ekspor KIB A)
            [
                'user_id'      => $kaurKeu?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Buku Inventaris (KIB A - Tanah) format Excel (.xlsx)',
                'subject_id'   => null,
                'subject_name' => 'KIB A - Tanah',
                'created_at'   => (clone $now)->subDays(3)->subHours(1),
            ],
            // 12. 3 Hari lalu (Update kondisi kursi tamu)
            [
                'user_id'      => $kasiPelayanan?->id ?? $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'ubah',
                'detail'       => 'mengubah kondisi inventaris ' . ($asetKursiTamu?->kode_barang ?? '3.05.02.01.999') . ' - ' . ($asetKursiTamu?->nama_barang ?? 'Kursi Tamu (1 set)') . ' menjadi Rusak Ringan',
                'subject_id'   => $asetKursiTamu?->id ?? 103,
                'subject_name' => $asetKursiTamu?->nama_barang ?? 'Kursi Tamu (1 set)',
                'created_at'   => (clone $now)->subDays(3)->subHours(4),
            ],
            // 13. 4 Hari lalu (Penambahan master ruangan)
            [
                'user_id'      => $admin->id,
                'module'       => 'Ruangan',
                'action'       => 'tambah',
                'detail'       => 'menambahkan ruangan ' . ($ruangPelayanan?->nama ?? 'Ruang Pelayanan Umum') . ' (' . ($ruangPelayanan?->kode ?? 'R-003') . ')',
                'subject_id'   => $ruangPelayanan?->id ?? 3,
                'subject_name' => $ruangPelayanan?->nama ?? 'Ruang Pelayanan Umum',
                'created_at'   => (clone $now)->subDays(4)->subHours(2),
            ],
            // 14. 5 Hari lalu (Penyesuaian data pejabat penandatangan)
            [
                'user_id'      => $admin->id,
                'module'       => 'Pejabat',
                'action'       => 'ubah',
                'detail'       => 'memperbarui data pejabat I Ketut Wijaya sebagai Kaur Keuangan penatausahaan aset',
                'subject_id'   => 99,
                'subject_name' => 'I Ketut Wijaya (Kaur Keuangan)',
                'created_at'   => (clone $now)->subDays(5)->subHours(3),
            ],
            // 15. 6 Hari lalu (Penambahan user operator baru)
            [
                'user_id'      => $admin->id,
                'module'       => 'User',
                'action'       => 'tambah',
                'detail'       => 'menambahkan pengguna Ni Komang Indah sebagai Staf Desa Operator Inventaris',
                'subject_id'   => $staf?->id ?? 19314,
                'subject_name' => 'Ni Komang Indah (Staf Desa)',
                'created_at'   => (clone $now)->subDays(6)->subHours(5),
            ],
            // 16. 1 Minggu lalu (Ekspor KIR Aula Desa)
            [
                'user_id'      => $sekdes?->id ?? $admin->id,
                'module'       => 'Export Data',
                'action'       => 'export',
                'detail'       => 'mengekspor Kartu Inventaris Ruangan (KIR) ' . ($aulaDesa?->nama ?? 'Aula Pertemuan Desa') . ' format PDF',
                'subject_id'   => $aulaDesa?->id ?? 5,
                'subject_name' => 'KIR ' . ($aulaDesa?->nama ?? 'Aula Pertemuan Desa'),
                'created_at'   => (clone $now)->subDays(7)->subHours(4),
            ],
            // 17. 8 Hari lalu (Update data lemari filling)
            [
                'user_id'      => $kaurPlan?->id ?? $admin->id,
                'module'       => 'Inventaris',
                'action'       => 'ubah',
                'detail'       => 'mengubah data inventaris ' . ($asetFilling?->kode_barang ?? '3.05.01.04.005') . ' - ' . ($asetFilling?->nama_barang ?? 'Filling Kabinet'),
                'subject_id'   => $asetFilling?->id ?? 111,
                'subject_name' => $asetFilling?->nama_barang ?? 'Filling Kabinet',
                'created_at'   => (clone $now)->subDays(8)->subHours(2),
            ],
            // 18. 10 Hari lalu (Penataan master jenis inventaris)
            [
                'user_id'      => $admin->id,
                'module'       => 'Jenis Inventaris',
                'action'       => 'tambah',
                'detail'       => 'menambahkan master jenis inventaris Peralatan dan Mesin (Permendagri 1/2016)',
                'subject_id'   => 30,
                'subject_name' => 'Peralatan dan Mesin',
                'created_at'   => (clone $now)->subDays(10)->subHours(6),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create([
                'user_id'      => $log['user_id'],
                'module'       => $log['module'],
                'action'       => $log['action'],
                'detail'       => $log['detail'],
                'subject_id'   => $log['subject_id'],
                'subject_name' => $log['subject_name'],
                'created_at'   => $log['created_at'],
                'updated_at'   => $log['created_at'],
            ]);
        }
    }
}
