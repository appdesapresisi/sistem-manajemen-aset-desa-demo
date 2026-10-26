<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'detail',
        'subject_id',
        'subject_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope query untuk mengecualikan log aktivitas yang dilakukan oleh Super Developer / Platform Owner.
     */
    public function scopeExcludeSuperDevelopers($query)
    {
        $devUsernames = User::getSuperDeveloperUsernames();

        return $query->where(function ($q) use ($devUsernames) {
            $q->whereNull('user_id')
              ->orWhereDoesntHave('user', function ($uq) use ($devUsernames) {
                  $uq->where(function ($userSub) use ($devUsernames) {
                      if (!empty($devUsernames)) {
                          $userSub->whereIn('username', $devUsernames);
                      }
                      $userSub->orWhereHas('role', function ($rq) {
                          $rq->whereIn('nama', ['super_developer', 'developer', 'owner']);
                      })
                      ->orWhereRaw("LOWER(nama) LIKE '%super developer%'")
                      ->orWhereRaw("LOWER(nama) LIKE '%platform owner%'");
                  });
              });
        });
    }

    /**
     * Dapatkan label nama modul yang rapi dan sesuai dengan nama menu di sidebar.
     */
    public function getModuleLabelAttribute(): string
    {
        $raw = strtolower(trim((string) $this->module));
        return match ($raw) {
            'inventaris', 'manajemen-aset', 'manajemen_aset', 'manajemenaset', 'buku-inventaris', 'buku_inventaris', 'bukuinventaris' => 'Buku Inventaris',
            'export', 'export-data', 'export_data', 'exportdata', 'pusat-ekspor', 'pusat_ekspor', 'pusatekspor' => 'Pusat Ekspor',
            'export_history', 'exporthistory', 'riwayat-ekspor', 'riwayat_ekspor', 'riwayatekspor' => 'Riwayat Ekspor',
            'penghapusan', 'penghapusan-aset', 'penghapusan_aset', 'penghapusanaset' => 'Penghapusan Aset',
            'jenisinventaris', 'jenis-inventaris', 'jenis_inventaris' => 'Jenis Inventaris',
            'ruangan', 'ruangans' => 'Ruangan',
            'dropdown', 'dropdowns', 'dropdown-dinamis', 'dropdown_dinamis' => 'Dropdown Dinamis',
            'bidang', 'bidangs' => 'Manajemen Bidang',
            'pejabat', 'pejabats', 'manajemen-pejabat', 'manajemen_pejabat' => 'Manajemen Pejabat',
            'user', 'users', 'manajemen-pengguna', 'manajemen_pengguna' => 'Manajemen Pengguna',
            'profile', 'profil', 'auth' => 'Profil',
            'aktivitas', 'aktivitas-sistem', 'aktivitas_sistem', 'activity', 'activity_log' => 'Aktivitas Sistem',
            'dashboard' => 'Dashboard',
            default => !empty($this->module) ? ucwords(str_replace(['_', '-'], ' ', (string) $this->module)) : '-',
        };
    }

    /**
     * Dapatkan label aksi/aktivitas dengan akhiran "Data", di mana aksi ubah/edit/update menjadi "Update Data".
     */
    public function getActionLabelAttribute(): string
    {
        $action = strtolower(trim((string) $this->action));
        if ($action === '') {
            return '-';
        }

        if (in_array($action, ['ubah', 'ubah data', 'edit', 'edit data', 'update', 'update data'])) {
            return 'Update Data';
        }

        $formatted = ucfirst(str_replace(['_', '-'], ' ', $action));
        if (str_ends_with(strtolower($formatted), ' data')) {
            return $formatted;
        }

        return $formatted . ' Data';
    }
}
