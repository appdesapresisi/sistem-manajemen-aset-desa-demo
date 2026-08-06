<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pejabat;

class PejabatPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function view(User $user, Pejabat $pejabat)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Pejabat $pejabat)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return (int) ($user->pejabat_id ?? 0) === (int) ($pejabat->id ?? 0);
    }

    public function delete(User $user, ?Pejabat $pejabat = null)
    {
        return $user->isSuperAdmin();
    }
}
