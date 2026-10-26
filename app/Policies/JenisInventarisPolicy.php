<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JenisInventaris;

class JenisInventarisPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function view(User $user, ?JenisInventaris $jenis = null)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ?JenisInventaris $jenis = null)
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ?JenisInventaris $jenis = null)
    {
        return $user->isSuperAdmin();
    }
}
