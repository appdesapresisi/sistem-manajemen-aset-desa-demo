<?php

namespace App\Policies;

use App\Models\Bidang;
use App\Models\User;

class BidangPolicy
{
    /**
     * Daftar bidang: Admin & Super Admin bisa lihat.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Detail bidang: Admin & Super Admin bisa lihat.
     */
    public function view(User $user, Bidang $bidang): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Tambah bidang: hanya Super Admin.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Edit bidang: hanya Super Admin.
     */
    public function update(User $user, Bidang $bidang): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Hapus bidang (single & bulk): hanya Super Admin.
     */
    public function delete(User $user, ?Bidang $bidang = null): bool
    {
        return $user->isSuperAdmin();
    }
}
