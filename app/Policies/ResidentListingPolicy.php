<?php

namespace App\Policies;

use App\Models\ResidentListing;
use App\Models\User;

class ResidentListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ResidentListing $residentListing): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, ResidentListing $residentListing): bool
    {
        return $user->id === $residentListing->user_id;
    }

    public function delete(User $user, ResidentListing $residentListing): bool
    {
        return $user->id === $residentListing->user_id;
    }

    public function renew(User $user, ResidentListing $residentListing): bool
    {
        return $user->id === $residentListing->user_id;
    }

    public function moderate(User $user, ResidentListing $residentListing): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }
}
