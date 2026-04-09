<?php

namespace App\Policies;

use App\Models\AdDuration;
use App\Models\User;

class AdDurationPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, AdDuration $adDuration): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    public function update(User $user, AdDuration $adDuration): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    public function delete(User $user, AdDuration $adDuration): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }
}
