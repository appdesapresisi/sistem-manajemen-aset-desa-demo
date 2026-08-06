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
            'tambah'  => '#10b981',  // hijau
            'ubah'    => '#f59e0b',  // kuning/orange
            'hapus'   => '#ef4444',  // merah
            'export'  => '#3b82f6',  // biru
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
            'ubah'    => 'mengubah',
            'hapus'   => 'menghapus',
            'export'  => 'mengekspor',
            default   => $action,
        };
    }
}
