<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ecommerce\UserOrder;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserOrder $order): bool
    {
        return $user->id === $order->buyer_id || $user->hasRole('admin') || $user->can('view all orders');
    }



    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, UserOrder $order): bool
    {
        return $user->id === $order->buyer_id
            && in_array($order->status, ['pending', 'processing']);
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, UserOrder $order): bool
    {
        return $user->id === $order->buyer_id
            && $order->status === 'pending';
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserOrder $userOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserOrder $userOrder): bool
    {
        return false;
    }

    /**
     * Optional: Admin override (called automatically if defined)
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true; // Admin can do everything
        }

        return null; // Use default logic otherwise
    }
}
