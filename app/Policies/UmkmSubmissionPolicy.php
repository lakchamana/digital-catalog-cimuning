<?php

namespace App\Policies;

use App\Models\UmkmSubmission;
use App\Models\User;

class UmkmSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, UmkmSubmission $submission): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, UmkmSubmission $submission): bool
    {
        return false;
    }

    public function delete(User $user, UmkmSubmission $submission): bool
    {
        return false;
    }
}
