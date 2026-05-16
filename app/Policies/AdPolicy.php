<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
{
    use HandlesAuthorization;

    /**
     * Before method is called before any other policy method.
     * If it returns true, the user is automatically authorized.
     */
    public function before(User $user, $ability)
    {
        if ($user->is_admin) {
            return true; // Admin can do everything
        }
    }

    /**
     * Determine if the user can view the ad.
     */
    public function view(User $user, Ad $ad): bool
    {
        // Only the creator can view (for normal users)
        return $user->id === $ad->user_id;
    }

    /**
     * Determine if the user can create an ad.
     */
    public function create(User $user): bool
    {
        // Only verified users can create ads
        return $user->is_verified;
    }

    /**
     * Determine if the user can update the ad.
     */
    public function update(User $user, Ad $ad): bool
    {
        return $user->id === $ad->user_id;
    }

    /**
     * Determine if the user can delete the ad.
     */
    public function delete(User $user, Ad $ad): bool
    {
        return $user->id === $ad->user_id;
    }

    /**
     * Custom view function for later logic.
     * You can define any additional conditions here.
     */
    public function specialView(User $user, Ad $ad): bool
    {
        // Placeholder: define your special view logic later
        return false;
    }
}
