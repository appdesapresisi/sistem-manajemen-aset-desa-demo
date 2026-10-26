<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan notifikasi uji coba / sampah lama
        Notification::truncate();

        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $this->seedNotificationsForUser($user);
        }
    }

    /**
     * Buat daftar notifikasi dinamis dan realistis sesuai tata kelola aset desa (Permendagri No. 1/2016).
     */
    private function seedNotificationsForUser(User $user): void
    {
        $now = Carbon::now();

        $notifications = [
            [
                'type' => 'asset',
                'title' => 'Inventaris Baru Terdata',
                'body' => '1 unit Laptop Lenovo ThinkPad L14 (KIB B - Peralatan & Mesin) berhasil ditambahkan oleh Kaur Tata Usaha.',
                'data' => [
                    'icon' => 'plus-circle',
                    'category' => 'KIB B',
                    'actor' => 'Kaur Tata Usaha',
                ],
                'url' => '/manajemen-aset',
                'is_read' => false,
                'created_at' => (clone $now)->subMinutes(12),
            ],
            [
                'type' => 'asset',
                'title' => 'Penghapusan Inventaris Selesai',
                'body' => 'Pencatatan penghapusan 4 unit Kursi Rapat Rusak Berat telah dibukukan dengan Berita Acara No. 042/BA-AS/2026.',
                'data' => [
                    'icon' => 'trash-2',
                    'category' => 'Penghapusan',
                    'actor' => 'Kaur Perencanaan',
                ],
                'url' => '/penghapusan-aset',
                'is_read' => false,
                'created_at' => (clone $now)->subMinutes(45),
            ],
            [
                'type' => 'asset',
                'title' => 'Perubahan Penempatan Ruangan Aset',
                'body' => 'Data penempatan 2 unit Proyektor Epson dialihkan ke Ruang Rapat BPD oleh Pengurus Barang.',
                'data' => [
                    'icon' => 'edit-2',
                    'category' => 'Ruangan',
                    'actor' => 'Pengurus Barang',
                ],
                'url' => '/ruangan',
                'is_read' => false,
                'created_at' => (clone $now)->subHours(2),
            ],
            [
                'type' => 'system',
                'title' => 'Ekspor Laporan Selesai',
                'body' => 'Buku Inventaris Tahunan TA 2025/2026 format Excel (.xlsx) berhasil digenerate dan siap diunduh.',
                'data' => [
                    'icon' => 'file-text',
                    'category' => 'Ekspor',
                    'actor' => 'Admin Desa',
                ],
                'url' => '/export-data/history',
                'is_read' => false,
                'created_at' => (clone $now)->subHours(5),
            ],
            [
                'type' => 'asset',
                'title' => 'Klasifikasi Inventaris Baru',
                'body' => 'Sub-kelompok "02.06.01 - Peralatan Studio & Komunikasi" ditambahkan ke master klasifikasi aset desa.',
                'data' => [
                    'icon' => 'plus-circle',
                    'category' => 'Klasifikasi',
                    'actor' => 'Admin Desa',
                ],
                'url' => '/jenis-inventaris',
                'is_read' => true,
                'created_at' => (clone $now)->subHours(14),
            ],
            [
                'type' => 'user',
                'title' => 'Pembaruan Pejabat Penatausahaan',
                'body' => 'Data SK Penugasan Pengurus Barang Milik Desa telah diperbarui oleh Sekretaris Desa.',
                'data' => [
                    'icon' => 'edit-2',
                    'category' => 'Pejabat',
                    'actor' => 'Sekretaris Desa',
                ],
                'url' => '/pejabat',
                'is_read' => true,
                'created_at' => (clone $now)->subDays(1),
            ],
            [
                'type' => 'user',
                'title' => 'Pendaftaran Akun Pengguna Baru',
                'body' => 'Akun operator baru atas nama Staff Tata Usaha telah berhasil didaftarkan ke sistem.',
                'data' => [
                    'icon' => 'plus-circle',
                    'category' => 'Pengguna',
                    'actor' => 'Super Admin',
                ],
                'url' => '/users',
                'is_read' => true,
                'created_at' => (clone $now)->subDays(2),
            ],
            [
                'type' => 'user',
                'title' => 'Pembaruan Kata Sandi Profil',
                'body' => 'Kata sandi akun profil Anda telah berhasil diperbarui demi keamanan akun.',
                'data' => [
                    'icon' => 'edit-2',
                    'category' => 'Keamanan',
                ],
                'url' => '/profile',
                'is_read' => true,
                'created_at' => (clone $now)->subDays(4),
            ],
        ];

        foreach ($notifications as $item) {
            Notification::create([
                'user_id'    => $user->id,
                'type'       => $item['type'],
                'title'      => $item['title'],
                'body'       => $item['body'],
                'data'       => $item['data'],
                'url'        => $item['url'],
                'is_read'    => $item['is_read'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['created_at'],
            ]);
        }
    }
}
