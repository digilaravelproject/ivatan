<?php

namespace App\Policies;

use App\Models\Ecommerce\UserService;
use App\Models\User;

class ServicePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }

    public function view(User $user, UserService $service): bool
    {
        return $user->id === $service->seller_id;
    }

    public function create(User $user): bool
    {
        return $user->is_seller;
    }

    public function update(User $user, UserService $service): bool
    {
        return $user->id === $service->seller_id;
    }

    public function delete(User $user, UserService $service): bool
    {
        return $user->id === $service->seller_id;
    }

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
