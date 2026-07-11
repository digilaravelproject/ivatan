<?php

namespace App\Services;

use App\Models\ExclusiveContentAccess;
use App\Models\ExclusiveContentPurchase;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPost;
use Exception;
use Illuminate\Support\Facades\DB;

class ExclusiveContentService
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get the active platform fee percentage or flat amount.
     * Returns ['type' => 'percentage'|'flat', 'value' => float]
     */
    public function getActivePlatformFee(UserPost $post, User $creator): array
    {
        // Level 3: Content-specific override
        if ($post->override_platform_fee !== null && $post->override_platform_fee_type) {
            return [
                'type' => $post->override_platform_fee_type,
                'value' => (float) $post->override_platform_fee,
            ];
        }

        // Level 2: User-specific override
        $enablement = $creator->enablement;
        if ($enablement && $enablement->override_platform_fee !== null && $enablement->override_platform_fee_type) {
            return [
                'type' => $enablement->override_platform_fee_type,
                'value' => (float) $enablement->override_platform_fee,
            ];
        }

        // Level 1: Global Platform Fee
        // Read from DB settings or config. Defaulting to 2% if not set.
        $globalType = Setting::where('key', 'exclusive_content_global_fee_type')->value('value') ?? 'percentage';
        $globalValue = Setting::where('key', 'exclusive_content_global_fee_value')->value('value') ?? 2;

        return [
            'type' => $globalType,
            'value' => (float) $globalValue,
        ];
    }

    /**
     * Calculate all price components for a purchase.
     */
    public function calculatePurchaseBreakdown(UserPost $post, User $creator): array
    {
        $creatorPrice = (float) $post->price;
        $feeConfig = $this->getActivePlatformFee($post, $creator);

        if ($feeConfig['type'] === 'percentage') {
            $platformFee = $creatorPrice * ($feeConfig['value'] / 100);
        } else {
            $platformFee = $feeConfig['value'];
        }

        // Gateway charges config
        $gatewayChargeBearer = Setting::where('key', 'exclusive_content_gateway_charge_bearer')->value('value') ?? 'buyer';
        
        // Mock gateway calculation (e.g. 2% + flat 3 INR)
        // Usually, gateway charges depend on the gateway. We'll set it to 0 or calculate dynamically based on gateway config.
        $gatewayCharge = 0; // In a real scenario, call PaymentOrchestrator to get fee.

        // Add-On Logic
        $finalPrice = $creatorPrice + $platformFee;
        
        if ($gatewayChargeBearer === 'buyer') {
            $finalPrice += $gatewayCharge;
        }

        return [
            'creator_price' => $creatorPrice,
            'platform_fee' => $platformFee,
            'gateway_charge' => $gatewayCharge,
            'gateway_charge_bearer' => $gatewayChargeBearer,
            'final_price' => $finalPrice,
        ];
    }

    /**
     * Process successful purchase.
     */
    public function processSuccessfulPurchase(ExclusiveContentPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === 'completed') {
                return; // Already processed
            }

            $purchase->status = 'completed';
            $purchase->save();

            // 1. Grant Access
            ExclusiveContentAccess::create([
                'user_id' => $purchase->buyer_id,
                'user_post_id' => $purchase->user_post_id,
                'purchase_id' => $purchase->id,
                'granted_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            // 2. Distribute Funds (Creator Share to Wallet)
            $creator = $purchase->post->user;
            
            // Calculate Creator's exact share.
            // Under Add-On logic, creator gets exactly Creator Price.
            // Unless gateway charge is borne by creator.
            $creatorEarnings = $purchase->creator_price;
            if ($purchase->gateway_charge_bearer === 'creator') {
                $creatorEarnings -= $purchase->gateway_charge_amount;
            }

            if ($creatorEarnings > 0) {
                $this->walletService->credit(
                    $creator->id,
                    $creatorEarnings,
                    ExclusiveContentPurchase::class,
                    $purchase->id,
                    "Earnings for Exclusive Content #{$purchase->user_post_id}",
                    $purchase->buyer_id,
                    $purchase->user_post_id
                );
            }
        });
    }

    /**
     * Revoke access for all users if content is deleted.
     */
    public function revokeAccessForContent(UserPost $post): void
    {
        // Since we have ON DELETE CASCADE on DB level for accesses, 
        // they might be removed automatically if the post is hard deleted.
        // If it's a soft delete, we can manually expire them.
        ExclusiveContentAccess::where('user_post_id', $post->id)
            ->update(['expires_at' => now()]);
    }

    /**
     * Refund a purchase manually within 24 hours.
     */
    public function issueRefund(ExclusiveContentPurchase $purchase, User $admin): void
    {
        if ($purchase->status !== 'completed') {
            throw new Exception("Purchase is not completed.");
        }

        if (now()->diffInHours($purchase->created_at) > 24) {
            throw new Exception("Refund window of 24 hours has expired.");
        }

        DB::transaction(function () use ($purchase) {
            $purchase->status = 'refunded';
            $purchase->refunded_at = now();
            $purchase->save();

            // Revoke access
            $access = $purchase->access;
            if ($access) {
                $access->expires_at = now();
                $access->save();
            }

            // Debit from Creator's wallet
            $creatorEarnings = $purchase->creator_price;
            if ($purchase->gateway_charge_bearer === 'creator') {
                $creatorEarnings -= $purchase->gateway_charge_amount;
            }

            if ($creatorEarnings > 0) {
                $this->walletService->debit(
                    $purchase->post->user_id,
                    $creatorEarnings,
                    ExclusiveContentPurchase::class,
                    $purchase->id,
                    "Refund for Exclusive Content #{$purchase->user_post_id}"
                );
            }
            
            // Note: Actual refund to Buyer's bank account should be triggered via Payment Gateway API here.
        });
    }
}
