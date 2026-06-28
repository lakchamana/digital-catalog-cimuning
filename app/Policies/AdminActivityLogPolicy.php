<?php

namespace App\Policies;

use App\Models\AdminActivityLog;
use App\Models\User;

class AdminActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasActiveAccount();
    }

    public function view(User $user, AdminActivityLog $log): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AdminActivityLog $log): bool
    {
        return false;
    }

    public function delete(User $user, AdminActivityLog $log): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
