<?php

namespace App\Policies;

use App\Models\BackupRun;
use App\Models\User;

class BackupRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasActiveAccount();
    }

    public function view(User $user, BackupRun $backupRun): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, BackupRun $backupRun): bool
    {
        return false;
    }

    public function delete(User $user, BackupRun $backupRun): bool
    {
        return false;
    }
}
