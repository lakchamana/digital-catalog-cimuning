<?php

namespace App\Policies;

use App\Models\RestoreRequest;
use App\Models\User;

class RestoreRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasActiveAccount();
    }

    public function view(User $user, RestoreRequest $restoreRequest): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, RestoreRequest $restoreRequest): bool
    {
        return false;
    }

    public function delete(User $user, RestoreRequest $restoreRequest): bool
    {
        return false;
    }
}
