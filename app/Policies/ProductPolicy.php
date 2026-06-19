<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isUmkmOwner();
    }

    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->umkm?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUmkmOwner() && $user->umkm()->exists();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isUmkmOwner() && $product->umkm?->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isUmkmOwner() && $product->umkm?->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
