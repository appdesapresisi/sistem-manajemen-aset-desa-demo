<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The relations to eager load on every query.
     *
     * @var array<string>
     */
    protected $with = ['role', 'bidang', 'dinas'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        // 'email', // email nullable, disimpan di DB tapi tidak digunakan aktif
        'password',
        'role_id',
        'pejabat_id',
        'bidang_id',
        'dinas_id',
        'is_aktif',
        'alamat',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_aktif' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Check if the user has a role.
     * Supports simple `role` string attribute, `role` relation, `roles` relation, or `role_id` -> Role lookup if Role model exists.
     */
    public function hasRole(string $role): bool
    {
        // direct attribute
        if (isset($this->role) && $this->role === $role) {
            return true;
        }

        // relation `role` (belongsTo)
        if (method_exists($this, 'role')) {
            try {
                $rel = $this->role;
                if ($rel && isset($rel->nama) && $rel->nama === $role) {
                    return true;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // relation `roles` (many-to-many)
        if (method_exists($this, 'roles')) {
            try {
                return $this->roles()->where('nama', $role)->exists();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // role_id lookup if Role model exists
        if (isset($this->role_id) && class_exists(\App\Models\Role::class)) {
            try {
                $r = \App\Models\Role::find($this->role_id);
                if ($r && isset($r->nama) && $r->nama === $role) {
                    return true;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return false;
    }

    /**
     * Cek apakah user saat ini adalah Super Developer / Platform Owner (pemilik sistem SaaS).
     * Otorisasi tingkat tertinggi lintas sistem untuk operasi infrastruktur kritis
     * seperti Cadangan & Pemulihan Basis Data (Database Backup & Restore).
     */
    public function isSuperDeveloper(): bool
    {
        // 1. Role khusus super_developer atau developer
        if ($this->hasRole('super_developer') || $this->hasRole('developer') || $this->hasRole('owner')) {
            return true;
        }

        // 2. Kolom flag jika ada di tabel users
        if (!empty($this->is_developer) || !empty($this->is_owner) || !empty($this->is_super_developer)) {
            return true;
        }

        // 3. Username cocok dengan daftar konfigurasi SUPER_DEVELOPER_USERNAMES
        $devUsernames = config('desa.super_developer_usernames', env('SUPER_DEVELOPER_USERNAMES', 'superdeveloper,developer,owner'));
        if (is_string($devUsernames)) {
            $devUsernames = array_filter(array_map('trim', explode(',', $devUsernames)));
        }
        if (is_array($devUsernames)) {
            $currentUsername = strtolower(trim((string) $this->username));
            foreach ($devUsernames as $u) {
                if ($currentUsername !== '' && $currentUsername === strtolower(trim((string) $u))) {
                    return true;
                }
            }
        }

        // 4. Email cocok dengan daftar konfigurasi SUPER_DEVELOPER_EMAILS
        $devEmails = config('desa.super_developer_emails', env('SUPER_DEVELOPER_EMAILS', ''));
        if (is_string($devEmails) && $devEmails !== '') {
            $devEmails = array_filter(array_map('trim', explode(',', $devEmails)));
            $currentEmail = strtolower(trim((string) $this->email));
            foreach ($devEmails as $e) {
                if ($currentEmail !== '' && $currentEmail === strtolower(trim((string) $e))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Ambil array daftar username Super Developer / Platform Owner dari konfigurasi/env.
     *
     * @return array<string>
     */
    public static function getSuperDeveloperUsernames(): array
    {
        $devUsernames = config('desa.super_developer_usernames', env('SUPER_DEVELOPER_USERNAMES', 'developer,owner,superdeveloper'));
        if (is_string($devUsernames)) {
            $devUsernames = array_filter(array_map('trim', explode(',', $devUsernames)));
        }
        return array_values((array) $devUsernames);
    }

    /**
     * Scope query untuk mengecualikan akun Super Developer / Platform Owner secara ketat (super strict).
     * Akun developer/owner dijamin tidak pernah masuk ke query data pengguna desa (tabel, statistik, export, dropdown).
     */
    public function scopeExcludeSuperDevelopers($query)
    {
        $usernames = static::getSuperDeveloperUsernames();
        if (!empty($usernames)) {
            $query->whereNotIn('username', $usernames);
        }

        // Juga kecualikan berdasarkan role jika ada
        $query->whereDoesntHave('role', function ($rq) {
            $rq->whereIn('nama', ['super_developer', 'developer', 'owner']);
        });

        return $query;
    }


    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }


    public function isSekretaris(): bool
    {
        return $this->hasRole('sekretaris') || $this->hasRole('sekretaris_desa');
    }

    public function isKaur(): bool
    {
        return $this->hasRole('kaur');
    }

    public function isKaurUmum(): bool
    {
        if (!$this->isKaur()) {
            return false;
        }

        $bidangNama = strtolower((string) optional($this->bidang)->nama);
        if (str_contains($bidangNama, 'umum') || str_contains($bidangNama, 'tata usaha')) {
            return true;
        }

        $jabatan = strtolower((string) optional($this->pejabat)->jabatan);
        if (str_contains($jabatan, 'umum') || str_contains($jabatan, 'tata usaha') || (bool) optional($this->pejabat)->is_petugas_barang) {
            return true;
        }

        return false;
    }

    /**
     * Otorisasi khusus penghapusan aset inventaris desa:
     * Hanya Kaur Umum (Pengurus Barang), Sekretaris Desa (Pengelola Barang),
     * Perbekel (PKPAD), dan Super Admin yang berhak menghapus aset.
     */
    public function canDeleteAset(): bool
    {
        return $this->isSuperDeveloper()
            || $this->isSuperAdmin()
            || $this->isPerbekel()
            || $this->isSekretaris()
            || $this->isKaurUmum();
    }

    public function isPerbekel(): bool
    {
        return $this->hasRole('perbekel');
    }

    public function isKasi(): bool
    {
        return $this->hasRole('kasi');
    }

    public function isKelianBanjar(): bool
    {
        return $this->hasRole('kelian_banjar');
    }

    public function isStaf(): bool
    {
        return $this->hasRole('staf');
    }

    public function isAdmin(): bool
    {
        return $this->isSuperDeveloper()
            || $this->hasRole('admin') || $this->hasRole('super_admin')
            || $this->hasRole('sekretaris') || $this->hasRole('sekretaris_desa')
            || $this->hasRole('kaur') || $this->hasRole('kasi') 
            || $this->hasRole('kelian_banjar') || $this->hasRole('staf')
            || $this->hasRole('perbekel');
    }

    /**
     * Relation to Role model (if using role_id on users table)
     */
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    /**
     * Relation to Pejabat model (if user is a pejabat)
     */
    public function pejabat()
    {
        return $this->belongsTo(\App\Models\Pejabat::class, 'pejabat_id');
    }

    /**
     * Relation to Bidang model (if user is Kaur)
     */
    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class, 'bidang_id');
    }

    /**
     * Relation to Dinas model (if user is Kelian Banjar)
     */
    public function dinas()
    {
        return $this->belongsTo(\App\Models\Dinas::class, 'dinas_id');
    }

    /**
     * Accessor: Generate avatar color based on name
     */
    public function getAvatarColorAttribute()
    {
        $colors = [
            '#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', 
            '#10b981', '#06b6d4', '#3b82f6', '#ef4444'
        ];
        $hash = crc32($this->nama ?? $this->username ?? 'user');
        // $hash = crc32($this->nama ?? $this->email ?? 'user'); // email nullable
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Accessor: Generate initials from name
     */
    public function getInitialsAttribute()
    {
        $name = $this->nama ?? $this->username ?? 'User';
        // $name = $this->nama ?? $this->email ?? 'User'; // email nullable
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Accessor: Get display name
     */
    public function getNamaDisplayAttribute()
    {
        return $this->nama ?? $this->username ?? 'User tidak diketahui';
        // return $this->nama ?? $this->email ?? 'User tidak diketahui'; // email nullable
    }

    /**
     * Accessor: Get role name for display
     */
    public function getRoleNameDisplayAttribute()
    {
        if ($this->role && isset($this->role->nama)) {
            $roleName = strtolower((string) $this->role->nama);

            if ($roleName === 'super_admin' || str_contains($roleName, 'super_admin')) {
                return 'Super Admin';
            }

            if (str_contains($roleName, 'sekretaris')) {
                return 'Sekretaris Desa';
            }

            if (str_contains($roleName, 'kaur')) {
                $bidangNama = trim((string) optional($this->bidang)->nama);
                return $bidangNama !== '' ? ('Kaur - ' . $bidangNama) : 'Kaur';
            }

            if (str_contains($roleName, 'kasi')) {
                $bidangNama = trim((string) optional($this->bidang)->nama);
                return $bidangNama !== '' ? ('Kasi - ' . $bidangNama) : 'Kasi';
            }

            if (str_contains($roleName, 'kelian_banjar')) {
                $dinasNama = trim((string) optional($this->dinas)->nama);
                return $dinasNama !== '' ? ('Kelian Banjar Dinas ' . $dinasNama) : 'Kelian Banjar';
            }

            if (str_contains($roleName, 'staf')) {
                $bidangNama = trim((string) optional($this->bidang)->nama);
                return $bidangNama !== '' ? ('Staf - ' . $bidangNama) : 'Staf';
            }

            if (str_contains($roleName, 'perbekel')) {
                return 'Perbekel';
            }

            return ucwords(str_replace('_', ' ', $this->role->nama));
        }
        return 'Tidak ada role';
    }

    /**
     * Accessor: Get role info array (class, icon, name)
     */
    public function getRoleInfoAttribute()
    {
        if (!$this->role) {
            return [
                'class' => 'viewer',
                'icon' => 'user',
                'name' => 'Tidak ada role'
            ];
        }

        $roleName = strtolower((string) ($this->role->nama ?? 'user'));

        if ($roleName === 'super_admin' || str_contains($roleName, 'super_admin')) {
            return [
                'class' => 'super-admin',
                'icon' => 'shield',
                'name' => 'Super Admin'
            ];
        }

        if (str_contains($roleName, 'sekretaris')) {
            return [
                'class' => 'admin',
                'icon' => 'shield',
                'name' => 'Sekretaris Desa'
            ];
        }

        if (str_contains($roleName, 'kaur')) {
            $bidangNama = trim((string) optional($this->bidang)->nama);
            return [
                'class' => 'admin',
                'icon' => 'shield',
                'name' => $bidangNama !== '' ? ('Kaur - ' . $bidangNama) : 'Kaur'
            ];
        }

        if (str_contains($roleName, 'kasi')) {
            $bidangNama = trim((string) optional($this->bidang)->nama);
            return [
                'class' => 'admin',
                'icon' => 'briefcase',
                'name' => $bidangNama !== '' ? ('Kasi - ' . $bidangNama) : 'Kasi'
            ];
        }

        if (str_contains($roleName, 'kelian_banjar')) {
            $dinasNama = trim((string) optional($this->dinas)->nama);
            return [
                'class' => 'kelian',
                'icon' => 'users',
                'name' => $dinasNama !== '' ? ('Kelian Banjar Dinas ' . $dinasNama) : 'Kelian Banjar'
            ];
        }

        if (str_contains($roleName, 'staf')) {
            $bidangNama = trim((string) optional($this->bidang)->nama);
            return [
                'class' => 'staf',
                'icon' => 'user-check',
                'name' => $bidangNama !== '' ? ('Staf - ' . $bidangNama) : 'Staf'
            ];
        }

        if (str_contains($roleName, 'perbekel')) {
            return [
                'class' => 'perbekel',
                'icon' => 'shield',
                'name' => 'Perbekel'
            ];
        }

        return [
            'class' => 'viewer',
            'icon' => 'user',
            'name' => ucwords(str_replace('_', ' ', $roleName))
        ];
    }
}
