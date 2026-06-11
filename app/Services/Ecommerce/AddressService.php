<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserAddress;

class AddressService
{
    /**
     * List user's saved addresses
     */
    public function listAddresses($user)
    {
        return UserAddress::where('user_id', $user->id)
            ->where('type', 'account')
            ->latest()
            ->get();
    }

    /**
     * Save a new address
     */
    public function storeAddress(array $data, $user)
    {
        return UserAddress::create(array_merge($data, [
            'user_id' => $user->id,
            'type' => 'account'
        ]));
    }
}
