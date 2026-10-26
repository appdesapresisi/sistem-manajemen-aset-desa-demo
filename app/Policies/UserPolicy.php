<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->isSuperDeveloper() || $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, ?User $model = null)
    {
        return $user->isSuperDeveloper() || $user->isSuperAdmin() || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isSuperDeveloper() || $user->isSuperAdmin();
    }

    public function update(User $user, ?User $model = null)
    {
        if ($model && $model->isSuperDeveloper() && !$user->isSuperDeveloper()) {
            return false;
        }
        return $user->isSuperDeveloper() || $user->isSuperAdmin();
    }

    public function delete(User $user, ?User $model = null)
    {
        if ($model && $model->isSuperDeveloper() && !$user->isSuperDeveloper()) {
            return false;
        }
        return $user->isSuperDeveloper() || $user->isSuperAdmin();
    }

    public function bulkDelete(User $user): bool
    {
        return $user->isSuperDeveloper() || $user->isSuperAdmin();
    }

}
