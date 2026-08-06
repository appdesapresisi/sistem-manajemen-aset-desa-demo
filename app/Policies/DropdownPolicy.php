<?php

namespace App\Policies;

use App\Models\Dropdown;
use App\Models\User;

class DropdownPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Dropdown $dropdown): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Dropdown $dropdown): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ?Dropdown $dropdown = null): bool
    {
        return $user->isSuperAdmin();
    }
}
