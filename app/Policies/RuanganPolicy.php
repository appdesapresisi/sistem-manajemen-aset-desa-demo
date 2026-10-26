<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ruangan;

class RuanganPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Ruangan $ruangan = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ?Ruangan $ruangan = null): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ?Ruangan $ruangan = null): bool
    {
        return $user->isSuperAdmin();
    }
}
