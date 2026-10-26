<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExportLog;

class ExportLogPolicy
{
    protected function canAccessExport(User $user): bool
    {
        return $user->isSuperDeveloper()
            || $user->isSuperAdmin()
            || $user->isSekretaris()
            || $user->isKaur()
            || $user->isKasi()
            || $user->isKelianBanjar()
            || $user->isStaf()
            || $user->isPerbekel();
    }

    /**
     * Determine if user can create/export files.
     * Used by all-jenis download authorization gate.
     */
    public function create(User $user)
    {
        return $this->canAccessExport($user);
    }

    /**
     * Determine if user can view export log
     * Admin can view all, regular users can only view their own
     */
    public function view(User $user, ExportLog $log)
    {
        return $this->canAccessExport($user) || $user->id === $log->user_id;
    }

    /**
     * Determine if user can view any export logs
     */
    public function viewAny(User $user)
    {
        return $this->canAccessExport($user);
    }

    /**
     * Determine if user can delete export log
     */
    public function delete(User $user, ExportLog $log)
    {
        return $this->canAccessExport($user) || $user->id === $log->user_id;
    }
}
