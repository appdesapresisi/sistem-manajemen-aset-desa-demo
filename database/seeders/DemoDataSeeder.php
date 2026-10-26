<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Bidang;
use App\Models\Dinas;
use App\Models\Pejabat;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\JenisInventaris;
use App\Models\JenisInventarisData;
use App\Models\Inventaris;
use App\Models\InventarisValue;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED ROLES
        $roles = [
            ['id' => 1, 'nama' => 'super_admin', 'tugas' => 'Super Administrator', 'deskripsi' => 'Akses penuh ke semua fitur dan pengaturan sistem'],
            ['id' => 2, 'nama' => 'sekretaris_desa', 'tugas' => 'Sekretaris Desa (Admin)', 'deskripsi' => 'Mengelola aset desa dan penatausahaan data'],
            ['id' => 3, 'nama' => 'kaur', 'tugas' => 'Kepala Urusan', 'deskripsi' => 'Mengelola aset pada bidang urusan masing-masing'],
            ['id' => 27, 'nama' => 'kasi', 'tugas' => 'Kepala Seksi', 'deskripsi' => 'Mengelola aset pada seksi masing-masing'],
            ['id' => 28, 'nama' => 'kelian_banjar', 'tugas' => 'Kelian Banjar Dinas', 'deskripsi' => 'Mengelola inventaris banjar dinas terkait'],
            ['id' => 29, 'nama' => 'staf', 'tugas' => 'Staf Desa', 'deskripsi' => 'Menginput dan memperbarui data inventaris'],
            ['id' => 10, 'nama' => 'perbekel', 'tugas' => 'Perbekel / Kepala Desa', 'deskripsi' => 'Melihat laporan dan menandatangani dokumen laporan aset'],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['id' => $r['id']], $r);
        }

        // 2. SEED BIDANG (10 items)
        $bidangs = [
            ['nama' => 'Bidang Pemerintahan Desa', 'deskripsi' => 'Urusan pemerintahan, administrasi, dan regulasi desa'],
            ['nama' => 'Bidang Pembangunan Desa', 'deskripsi' => 'Pembangunan sarana prasarana fisik desa'],
            ['nama' => 'Bidang Pembinaan Kemasyarakatan', 'deskripsi' => 'Ketertiban, kebudayaan, dan keagamaan'],
            ['nama' => 'Bidang Pemberdayaan Masyarakat', 'deskripsi' => 'Ekonomi, kesehatan, dan kapasitas warga'],
            ['nama' => 'Bidang Penanggulangan Bencana', 'deskripsi' => 'Keadaan darurat dan bencana alam'],
            ['nama' => 'Bidang Keuangan', 'deskripsi' => 'Pengelolaan keuangan dan anggaran APBDes'],
            ['nama' => 'Bidang Perencanaan', 'deskripsi' => 'Penyusunan RKPDes dan RPJMDes'],
            ['nama' => 'Bidang Umum dan Protokol', 'deskripsi' => 'Pelayanan administrasi umum dan surat menyurat'],
            ['nama' => 'Bidang Kesejahteraan Rakyat', 'deskripsi' => 'Pelayanan sosial dan kesejahteraan warga'],
            ['nama' => 'Bidang Keamanan dan Ketertiban', 'deskripsi' => 'Linmas dan pos keamanan desa'],
        ];

        $bidangIds = [];
        foreach ($bidangs as $b) {
            $created = Bidang::updateOrCreate(['nama' => $b['nama']], $b);
            $bidangIds[] = $created->id;
        }

        // 3. SEED DINAS / BANJAR DINAS (10 items)
        $dinasList = [
            ['nama' => 'Banjar Dinas Utama', 'keterangan' => 'Wilayah pusat administrasi'],
            ['nama' => 'Banjar Dinas Madya', 'keterangan' => 'Wilayah pemukiman padat'],
            ['nama' => 'Banjar Dinas Kaja', 'keterangan' => 'Wilayah utara'],
            ['nama' => 'Banjar Dinas Kelod', 'keterangan' => 'Wilayah selatan'],
            ['nama' => 'Banjar Dinas Kangin', 'keterangan' => 'Wilayah timur'],
            ['nama' => 'Banjar Dinas Kauh', 'keterangan' => 'Wilayah barat'],
            ['nama' => 'Banjar Dinas Asri', 'keterangan' => 'Wilayah perumahan baru'],
            ['nama' => 'Banjar Dinas Sentana', 'keterangan' => 'Wilayah pegunungan'],
            ['nama' => 'Banjar Dinas Mekar', 'keterangan' => 'Wilayah pertanian'],
            ['nama' => 'Banjar Dinas Sari', 'keterangan' => 'Wilayah perdagangan'],
        ];

        $dinasIds = [];
        foreach ($dinasList as $d) {
            $created = Dinas::updateOrCreate(['nama' => $d['nama']], $d);
            $dinasIds[] = $created->id;
        }

        // 4. SEED PEJABAT (10 items)
        $pejabats = [
            ['nama' => 'I Wayan Pratama', 'jabatan' => 'Perbekel', 'tugas' => 'Mengambil keputusan tertinggi desa', 'alamat' => 'Jl. Utama No. 1', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'Ni Made Lestari', 'jabatan' => 'Sekretaris Desa', 'tugas' => 'Koordinator administrasi keuangan dan aset', 'alamat' => 'Jl. Mawar No. 12', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'I Ketut Wijaya', 'jabatan' => 'Kaur Keuangan', 'tugas' => 'Bendahara pengeluaran desa', 'alamat' => 'Jl. Melati No. 5', 'is_aktif' => 1, 'is_petugas_barang' => 1],
            ['nama' => 'Ni Nyoman Sri', 'jabatan' => 'Kaur Perencanaan', 'tugas' => 'Penyusunan rencana pembangunan', 'alamat' => 'Jl. Dahlia No. 8', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'I Gede Budi', 'jabatan' => 'Kasi Pemerintahan', 'tugas' => 'Pengelolaan data kependudukan dan aset wilayah', 'alamat' => 'Jl. Kamboja No. 3', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'I Made Suarta', 'jabatan' => 'Kasi Kesejahteraan', 'tugas' => 'Pelaksana pembangunan fisik desa', 'alamat' => 'Jl. Anggrek No. 15', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'Ni Putu Ayu', 'jabatan' => 'Kasi Pelayanan', 'tugas' => 'Pelayanan administrasi umum masyarakat', 'alamat' => 'Jl. Kenanga No. 22', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'I Wayan Sudiarta', 'jabatan' => 'Kelian Banjar Utama', 'tugas' => 'Kepala dusun Banjar Utama', 'alamat' => 'Jl. Flamboyan No. 9', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'I Made Sumerta', 'jabatan' => 'Kelian Banjar Madya', 'tugas' => 'Kepala dusun Banjar Madya', 'alamat' => 'Jl. Tulip No. 18', 'is_aktif' => 1, 'is_petugas_barang' => 0],
            ['nama' => 'Ni Komang Indah', 'jabatan' => 'Staf Pemerintahan', 'tugas' => 'Operator sistem inventaris desa', 'alamat' => 'Jl. Cempaka No. 7', 'is_aktif' => 1, 'is_petugas_barang' => 1],
        ];

        $pejabatIds = [];
        foreach ($pejabats as $p) {
            $created = Pejabat::updateOrCreate(['nama' => $p['nama']], $p);
            $pejabatIds[] = $created->id;
        }

        // 5. SEED USERS (10 items + Super Developer/Owner)
        $usersData = [
            ['nama' => 'Platform Owner', 'username' => 'owner', 'password' => 'owner123', 'role_id' => 1, 'pejabat_id' => null, 'bidang_id' => null, 'dinas_id' => null, 'alamat' => 'Kantor Pusat Developer'],
            ['nama' => 'Super Developer', 'username' => 'developer', 'password' => 'developer123', 'role_id' => 1, 'pejabat_id' => null, 'bidang_id' => null, 'dinas_id' => null, 'alamat' => 'Kantor Pusat Developer'],
            ['nama' => 'Super Admin Demo', 'username' => 'superadmin', 'password' => 'admin123', 'role_id' => 1, 'pejabat_id' => null, 'bidang_id' => null, 'dinas_id' => null, 'alamat' => 'Kantor Desa'],
            ['nama' => 'Ni Made Lestari', 'username' => 'sekdes', 'password' => 'password', 'role_id' => 2, 'pejabat_id' => $pejabatIds[1], 'bidang_id' => $bidangIds[7], 'dinas_id' => null, 'alamat' => 'Jl. Mawar No. 12'],
            ['nama' => 'I Ketut Wijaya', 'username' => 'kaurkeu', 'password' => 'password', 'role_id' => 3, 'pejabat_id' => $pejabatIds[2], 'bidang_id' => $bidangIds[5], 'dinas_id' => null, 'alamat' => 'Jl. Melati No. 5'],
            ['nama' => 'Ni Nyoman Sri', 'username' => 'kaurplan', 'password' => 'password', 'role_id' => 3, 'pejabat_id' => $pejabatIds[3], 'bidang_id' => $bidangIds[6], 'dinas_id' => null, 'alamat' => 'Jl. Dahlia No. 8'],
            ['nama' => 'I Gede Budi', 'username' => 'kasipem', 'password' => 'password', 'role_id' => 27, 'pejabat_id' => $pejabatIds[4], 'bidang_id' => $bidangIds[0], 'dinas_id' => null, 'alamat' => 'Jl. Kamboja No. 3'],
            ['nama' => 'I Made Suarta', 'username' => 'kasikesra', 'password' => 'password', 'role_id' => 27, 'pejabat_id' => $pejabatIds[5], 'bidang_id' => $bidangIds[8], 'dinas_id' => null, 'alamat' => 'Jl. Anggrek No. 15'],
            ['nama' => 'Ni Putu Ayu', 'username' => 'kasipelayanan', 'password' => 'password', 'role_id' => 27, 'pejabat_id' => $pejabatIds[6], 'bidang_id' => $bidangIds[1], 'dinas_id' => null, 'alamat' => 'Jl. Kenanga No. 22'],
            ['nama' => 'I Wayan Sudiarta', 'username' => 'kelianutama', 'password' => 'password', 'role_id' => 28, 'pejabat_id' => $pejabatIds[7], 'bidang_id' => null, 'dinas_id' => $dinasIds[0], 'alamat' => 'Jl. Flamboyan No. 9'],
            ['nama' => 'Ni Komang Indah', 'username' => 'stafpem', 'password' => 'password', 'role_id' => 29, 'pejabat_id' => $pejabatIds[9], 'bidang_id' => $bidangIds[0], 'dinas_id' => null, 'alamat' => 'Jl. Cempaka No. 7'],
            ['nama' => 'I Wayan Pratama', 'username' => 'perbekel', 'password' => 'password', 'role_id' => 10, 'pejabat_id' => $pejabatIds[0], 'bidang_id' => null, 'dinas_id' => null, 'alamat' => 'Jl. Utama No. 1'],
        ];

        $userIds = [];
        foreach ($usersData as $ud) {
            $user = User::updateOrCreate(
                ['username' => $ud['username']],
                [
                    'nama' => $ud['nama'],
                    'password' => Hash::make($ud['password']),
                    'role_id' => $ud['role_id'],
                    'pejabat_id' => $ud['pejabat_id'],
                    'bidang_id' => $ud['bidang_id'],
                    'dinas_id' => $ud['dinas_id'],
                    'alamat' => $ud['alamat'],
                    'is_aktif' => 1,
                ]
            );
            $userIds[] = $user->id;
        }

        // 6. SEED RUANGAN (10 items)
        $ruangans = [
            ['nama' => 'Ruang Perbekel', 'kode' => 'R-001', 'deskripsi' => 'Ruang kerja Kepala Desa', 'penanggung_jawab_id' => $userIds[9]],
            ['nama' => 'Ruang Sekretaris Desa', 'kode' => 'R-002', 'deskripsi' => 'Ruang kerja Sekdes', 'penanggung_jawab_id' => $userIds[1]],
            ['nama' => 'Ruang Pelayanan Umum', 'kode' => 'R-003', 'deskripsi' => 'Loket pelayanan surat-menyurat warga', 'penanggung_jawab_id' => $userIds[6]],
            ['nama' => 'Ruang Staf Kaur & Kasi', 'kode' => 'R-004', 'deskripsi' => 'Ruang kerja bersama perangkat desa', 'penanggung_jawab_id' => $userIds[2]],
            ['nama' => 'Aula Pertemuan Desa', 'kode' => 'R-005', 'deskripsi' => 'Ruang rapat besar dan sosialisasi', 'penanggung_jawab_id' => $userIds[1]],
            ['nama' => 'Gudang Inventaris Utama', 'kode' => 'R-006', 'deskripsi' => 'Tempat penyimpanan peralatan kantor', 'penanggung_jawab_id' => $userIds[8]],
            ['nama' => 'Ruang Rapat BPD', 'kode' => 'R-007', 'deskripsi' => 'Ruang diskusi Badan Permusyawaratan Desa', 'penanggung_jawab_id' => $userIds[4]],
            ['nama' => 'Pos Keamanan Linmas', 'kode' => 'R-008', 'deskripsi' => 'Gedung jaga keamanan depan gerbang', 'penanggung_jawab_id' => $userIds[8]],
            ['nama' => 'Perpustakaan Desa', 'kode' => 'R-009', 'deskripsi' => 'Ruang baca dan arsip buku desa', 'penanggung_jawab_id' => $userIds[5]],
            ['nama' => 'Ruang PKK dan Pemberdayaan', 'kode' => 'R-010', 'deskripsi' => 'Ruang kegiatan pemberdayaan wanita', 'penanggung_jawab_id' => $userIds[5]],
        ];

        $ruanganIds = [];
        foreach ($ruangans as $ru) {
            $created = Ruangan::updateOrCreate(['nama' => $ru['nama']], $ru);
            $ruanganIds[] = $created->id;
        }

        // 7. SEED JENIS INVENTARIS (1 item)
        $jenisList = [
            ['nama' => 'Aset Tanah', 'deskripsi' => 'Tanah kas desa, kuburan, dan pekarangan desa', 'icon_mode' => 'auto', 'requires_ruangan' => 0, 'kib_type' => 'A'],
        ];

        $jenisIds = [];
        foreach ($jenisList as $jl) {
            $created = JenisInventaris::updateOrCreate(['nama' => $jl['nama']], $jl);
            $jenisIds[] = $created->id;

            // Seed some default fields for each Jenis
            // Kami pastikan field standard terpasang.
            // Baris standard: Merek/Tipe, Nomor Identitas/Sertifikat, Ukuran, Asal Usul.
            $fields = [
                ['nama_field' => 'merek_tipe', 'label_field' => 'Merek / Tipe', 'tipe' => 'string', 'is_required' => 0, 'order' => 1],
                ['nama_field' => 'nomor_sertifikat', 'label_field' => 'Nomor Sertifikat/Dokumen', 'tipe' => 'string', 'is_required' => 0, 'order' => 2],
                ['nama_field' => 'ukuran_luas', 'label_field' => 'Ukuran / Luas (m2)', 'tipe' => 'string', 'is_required' => 0, 'order' => 3],
                ['nama_field' => 'bahan_material', 'label_field' => 'Bahan / Material', 'tipe' => 'string', 'is_required' => 0, 'order' => 4],
            ];

            foreach ($fields as $fd) {
                JenisInventarisData::updateOrCreate(
                    [
                        'jenis_inventaris_id' => $created->id,
                        'nama_field' => $fd['nama_field']
                    ],
                    [
                        'label_field' => $fd['label_field'],
                        'field_type' => 'custom',
                        'tipe' => $fd['tipe'],
                        'is_required' => $fd['is_required'],
                        'include_in_export' => 1,
                        'order' => $fd['order']
                    ]
                );
            }
        }

        // 8. SEED INVENTARIS & VALUES (1 item)
        $inventarisData = [
            [
                'jenis_inventaris_id' => $jenisIds[0], // Aset Tanah
                'nama_barang' => 'Tanah Kantor Desa',
                'jenis_barang' => 'Tanah',
                'satuan' => 'M2',
                'kode_barang' => '1.01.01.01.001',
                'nup' => '0001',
                'tahun_perolehan' => 2010,
                'nilai_perolehan' => 150000000.00,
                'keterangan' => 'Sertifikat hak pakai desa',
                'asal_usul' => 'kekayaan_asli',
                'pengguna' => 'Pemerintah Desa',
                'ruangan_id' => null,
                'kondisi' => 'baik',
                'values' => [
                    'merek_tipe' => '-',
                    'nomor_sertifikat' => 'HP-02.01.002.2010',
                    'ukuran_luas' => '1500',
                    'bahan_material' => 'Tanah Pekarangan',
                ]
            ],
        ];

        foreach ($inventarisData as $idat) {
            $inv = Inventaris::updateOrCreate(
                [
                    'kode_barang' => $idat['kode_barang'],
                    'nup' => $idat['nup']
                ],
                [
                    'jenis_inventaris_id' => $idat['jenis_inventaris_id'],
                    'nama_barang' => $idat['nama_barang'],
                    'jenis_barang' => $idat['jenis_barang'],
                    'satuan' => $idat['satuan'],
                    'tahun_perolehan' => $idat['tahun_perolehan'],
                    'nilai_perolehan' => $idat['nilai_perolehan'],
                    'keterangan' => $idat['keterangan'],
                    'asal_usul' => $idat['asal_usul'],
                    'pengguna' => $idat['pengguna'],
                    'ruangan_id' => $idat['ruangan_id'],
                    'kondisi' => $idat['kondisi'],
                    'status' => 'disetujui',
                    'created_by' => $userIds[0], // Super Admin
                    'updated_by' => null,
                ]
            );

            // Seed field values
            // Ambil field list dari jenis
            $fields = JenisInventarisData::where('jenis_inventaris_id', $inv->jenis_inventaris_id)->get();
            foreach ($fields as $f) {
                $val = $idat['values'][$f->nama_field] ?? '-';
                InventarisValue::updateOrCreate(
                    [
                        'inventaris_id' => $inv->id,
                        'jenis_inventaris_data_id' => $f->id
                    ],
                    [
                        'value' => $val
                    ]
                );
            }
        }

        // 9. SEED RECENT ACTIVITIES
        \App\Models\ActivityLog::truncate();

        $activities = [
            [
                'user_id' => $userIds[0], // Super Admin
                'module' => 'user',
                'action' => 'tambah',
                'detail' => 'menambahkan pengguna baru "Ni Made Lestari"',
                'created_at' => now()->subMinutes(15),
            ],
            [
                'user_id' => $userIds[1], // Ni Made Lestari
                'module' => 'profile',
                'action' => 'ubah',
                'detail' => 'memperbarui informasi profil pengguna',
                'created_at' => now()->subMinutes(45),
            ],
            [
                'user_id' => $userIds[0], // Super Admin
                'module' => 'inventaris',
                'action' => 'tambah',
                'detail' => 'menambahkan aset baru "Tanah Kantor Desa"',
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $userIds[2], // I Ketut Wijaya
                'module' => 'export',
                'action' => 'export',
                'detail' => 'melakukan ekspor laporan "Inventaris Peralatan dan Mesin" ke Excel',
                'created_at' => now()->subHours(4),
            ],
            [
                'user_id' => $userIds[0], // Super Admin
                'module' => 'inventaris',
                'action' => 'ubah',
                'detail' => 'memperbarui kondisi aset "Motor Honda Vario Dinas"',
                'created_at' => now()->subHours(8),
            ],
            [
                'user_id' => $userIds[8], // Ni Komang Indah
                'module' => 'ruangan',
                'action' => 'ubah',
                'detail' => 'mengubah detail ruangan "Ruang Pelayanan Umum"',
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($activities as $act) {
            \App\Models\ActivityLog::create($act);
        }

        // 10. DISTRIBUTE ASSETS CREATED_AT OVER THE PAST 12 MONTHS FOR TREND GRAPH
        $allAssets = \App\Models\Inventaris::all();
        foreach ($allAssets as $idx => $asset) {
            // Kita bagi rata dalam 12 bulan terakhir agar grafiknya menyebar merata
            $monthsAgo = $idx % 12;
            $randomDays = rand(1, 28);
            $randomHours = rand(1, 23);
            $simulatedDate = now()->subMonths($monthsAgo)->subDays($randomDays)->subHours($randomHours);

            $asset->timestamps = false;
            $asset->created_at = $simulatedDate;
            $asset->updated_at = $simulatedDate;
            $asset->save();
        }

        // 11. SEED DUMMY EXPORT LOGS (10 Contextual Records)
        $this->call(ExportLogSeeder::class);

        // 12. SEED DYNAMIC NOTIFICATIONS FOR ALL USERS
        $this->call(NotificationSeeder::class);
    }
}
