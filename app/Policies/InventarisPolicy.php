<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inventaris;

class InventarisPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin() 
            || $user->isKasi() || $user->isKelianBanjar() || $user->isStaf();
    }

    public function view(User $user, Inventaris $inventaris)
    {
        return $user->isAdmin() || $user->isSuperAdmin()
            || $user->isKasi() || $user->isKelianBanjar() || $user->isStaf();
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin()
            || $user->isKasi() || $user->isKelianBanjar() || $user->isStaf();
    }

    public function update(User $user, Inventaris $inventaris)
    {
        return $user->isAdmin() || $user->isSuperAdmin()
            || $user->isKasi() || $user->isKelianBanjar() || $user->isStaf();
    }

    public function delete(User $user, Inventaris $inventaris)
    {
        return $user->isSuperAdmin();
    }

    public function approve(User $user, Inventaris $inventaris)
    {
        // Approval workflow removed — method kept for backward compat but always denies
        return false;
    }
}
