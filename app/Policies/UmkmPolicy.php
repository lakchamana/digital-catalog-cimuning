<?php

namespace App\Policies;

use App\Models\Umkm;
use App\Models\User;

class UmkmPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isUmkmOwner();
    }

    public function view(User $user, Umkm $umkm): bool
    {
        return $user->isAdmin() || $umkm->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUmkmOwner() && ! $user->umkm()->exists();
    }

    public function update(User $user, Umkm $umkm): bool
    {
        return $user->isUmkmOwner() && $umkm->user_id === $user->id;
    }

    public function delete(User $user, Umkm $umkm): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
