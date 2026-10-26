<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Tulis satu entri log aktivitas.
     *
     * @param  string       $module       'User' | 'Pejabat' | 'JenisInventaris' | 'Inventaris'
     * @param  string       $action       'tambah' | 'ubah' | 'hapus' | 'approve' | 'reject' | 'export'
     * @param  string       $detail       Deskripsi teks lengkap, misal "Menambahkan user Budi"
     * @param  int|null     $subjectId    ID record yang terpengaruh
     * @param  string|null  $subjectName  Nama/label record yang terpengaruh
     */
    public static function log(
        string $module,
        string $action,
        string $detail,
        ?int $subjectId = null,
        ?string $subjectName = null
    ): void {
        try {
            $user = Auth::user();
            if ($user instanceof \App\Models\User && method_exists($user, 'isSuperDeveloper') && $user->isSuperDeveloper()) {
                return;
            }

            ActivityLog::create([
                'user_id'      => Auth::id(),
                'module'       => $module,
                'action'       => $action,
                'detail'       => $detail,
                'subject_id'   => $subjectId,
                'subject_name' => $subjectName,
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai gagal log membatalkan proses utama
            Log::warning('ActivityLogger::log failed: ' . $e->getMessage());
        }
    }

    /**
     * Warna dot untuk setiap jenis action (digunakan di dashboard).
     */
    public static function colorFor(string $action): string
    {
        return match ($action) {
            'tambah', 'restore' => '#10b981',  // hijau
            'ubah', 'update' => '#6366f1',  // biru-ungu
            'hapus'   => '#ef4444',  // merah
            'approve' => '#059669',  // emerald
            'reject'  => '#dc2626',  // merah tua
            'export'  => '#f59e0b',  // kuning
            default   => '#6b7280',  // abu
        };
    }

    /**
     * Label Indonesia untuk action.
     */
    public static function labelFor(string $action): string
    {
        return match ($action) {
            'tambah'  => 'menambahkan',
            'ubah', 'update' => 'mengupdate',
            'hapus'   => 'menghapus',
            'restore' => 'memulihkan',
            'export'  => 'mengekspor',
            default   => $action,
        };
    }

    /**
     * Nama modul resmi yang sesuai dengan menu sidebar.
     */
    public static function moduleLabel(?string $module): string
    {
        $raw = strtolower(trim((string) $module));
        return match ($raw) {
            'inventaris', 'manajemen-aset', 'manajemen_aset', 'manajemenaset' => 'Manajemen Aset',
            'export', 'export-data', 'export_data', 'exportdata' => 'Export Data',
            'penghapusan', 'penghapusan-aset', 'penghapusan_aset', 'penghapusanaset' => 'Penghapusan Aset',
            'jenisinventaris', 'jenis-inventaris', 'jenis_inventaris' => 'Jenis Inventaris',
            'ruangan', 'ruangans' => 'Ruangan',
            'dropdown', 'dropdowns' => 'Dropdown',
            'bidang', 'bidangs' => 'Bidang',
            'pejabat', 'pejabats' => 'Pejabat',
            'user', 'users' => 'Users',
            'profile', 'profil', 'auth' => 'Profil',
            default => !empty($module) ? ucwords(str_replace(['_', '-'], ' ', (string) $module)) : '-',
        };
    }
}
