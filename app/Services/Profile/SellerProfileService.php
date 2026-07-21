<?php

namespace App\Services\Profile;

use App\Models\Profile;
use App\Models\SellerDetail;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerProfileService
{
    const SELLER_TYPES = ['products', 'services', 'both'];

    public function validateSellerType(string $sellerType): void
    {
        if (!in_array($sellerType, self::SELLER_TYPES)) {
            throw new \InvalidArgumentException(
                'Seller type must be one of: ' . implode(', ', self::SELLER_TYPES)
            );
        }
    }

    public function isFreeServiceListingAllowed(): bool
    {
        $val = \App\Models\Setting::where('key', 'allow_free_service_listing')->value('value') ?? '1';
        return in_array(strtolower((string)$val), ['1', 'true', 'yes'], true);
    }

    public function validateSellerTypeChange(SellerDetail $sellerDetail, string $newType): void
    {
        $this->validateSellerType($newType);

        if ($newType === 'both' || ($sellerDetail->seller_type !== 'both' && $newType === 'both')) {
            if ($this->isFreeServiceListingAllowed()) {
                return; // Dynamic setting allows free service listing
            }

            $profile = $sellerDetail->profile;

            $hasActiveSub = UserSubscription::where('profile_id', $profile->id)
                ->whereIn('status', ['active', 'past_due'])
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->exists();

            if (!$hasActiveSub) {
                throw new \RuntimeException(
                    'A subscription is required to sell both products and services. ' .
                    'Please purchase a subscription first.'
                );
            }
        }
    }

    public function updateSellerType(Profile $profile, string $sellerType, array $details = []): SellerDetail
    {
        $this->validateSellerType($sellerType);

        return DB::transaction(function () use ($profile, $sellerType, $details) {
            $sellerDetail = $profile->sellerDetails;

            if ($sellerDetail && $sellerDetail->seller_type !== $sellerType && $sellerType === 'both') {
                $this->validateSellerTypeChange($sellerDetail, $sellerType);
            }

            $data = array_merge(
                ['seller_type' => $sellerType],
                $details
            );

            if ($sellerDetail) {
                $sellerDetail->update($data);
            } else {
                $sellerDetail = $profile->sellerDetails()->create($data);
            }

            Log::info("Seller type updated", [
                'profile_id' => $profile->id,
                'seller_type' => $sellerType,
            ]);

            return $sellerDetail->fresh();
        });
    }

    public function canSellProducts(Profile $profile): bool
    {
        $details = $profile->sellerDetails;
        if (!$details) {
            return false;
        }

        if ($details->sellsBoth()) {
            return $this->hasActiveSubscription($profile) || $this->isFreeServiceListingAllowed();
        }

        return $details->sellsProducts();
    }

    public function canSellServices(Profile $profile): bool
    {
        $details = $profile->sellerDetails;
        if (!$details) {
            return false;
        }

        if ($this->isFreeServiceListingAllowed()) {
            return $details->sellsServices() || $details->sellsBoth();
        }

        if ($details->sellsBoth()) {
            return $this->hasActiveSubscription($profile);
        }

        return $details->sellsServices();
    }

    protected function hasActiveSubscription(Profile $profile): bool
    {
        return $profile->activeSubscription !== null;
    }
}
