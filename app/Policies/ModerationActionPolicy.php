<?php

namespace App\Policies;

use App\Models\ModerationAction;
use App\Models\User;

class ModerationActionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ModerationAction $action): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ModerationAction $action): bool
    {
        return false;
    }

    public function delete(User $user, ModerationAction $action): bool
    {
        return false;
    }
}
