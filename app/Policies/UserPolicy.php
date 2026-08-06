<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, User $model)
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $model)
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, User $model)
    {
        return $user->isSuperAdmin();
    }

    public function bulkDelete(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
