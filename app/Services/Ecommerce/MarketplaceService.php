<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;

class MarketplaceService
{
    /**
     * Get products for the marketplace
     */
    public function getProducts(array $filters, $user)
    {
        $query = UserProduct::with(['images', 'seller'])
            ->whereIn('status', ['active', 'approved']);

        if ($user) {
            $query->where('seller_id', '!=', $user->id);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(12);
    }

    /**
     * Get services for the marketplace
     */
    public function getServices(array $filters, $user)
    {
        $query = UserService::with(['images', 'seller'])
            ->whereIn('status', ['active', 'approved']);

        if ($user) {
            $query->where('seller_id', '!=', $user->id);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(12);
    }
}
