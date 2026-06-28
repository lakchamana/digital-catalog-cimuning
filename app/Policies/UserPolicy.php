<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasActiveAccount();
    }

    public function view(User $user, User $owner): bool
    {
        return $user->isAdmin() && $user->hasActiveAccount() && $owner->isUmkmOwner();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $owner): bool
    {
        return $this->view($user, $owner) && ! $user->is($owner);
    }

    public function delete(User $user, User $owner): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
