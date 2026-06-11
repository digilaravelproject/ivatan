<?php

namespace App\Services\Profile;

use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProfileConfigService
{
    public function getConfig(User $user): array
    {
        return Cache::remember("profile_config:{$user->id}", 300, function () use ($user) {
            try {
                $user->load([
                    'media',
                    'profiles' => fn($q) => $q->with([
                        'sellerDetails',
                        'employerDetails',
                        'musicDetails',
                        'creatorDetails',
                        'activeSubscription.plan',
                    ]),
                ]);

                // 1. Calculate first_profile (Immutable sign-up profile)
                $registrationProfile = $user->profiles->first(fn($p) => $p->type !== 'personal');
                $firstProfileName = $registrationProfile ? $registrationProfile->type : 'personal';

                // 2. Calculate unlocked_profiles (Flattened list of profiles with active subscriptions)
                $unlockedProfiles = [];
                foreach ($user->profiles as $profile) {
                    if ($profile->type === 'personal') {
                        $unlockedProfiles[] = 'personal';
                        continue;
                    }
                    $sub = $profile->activeSubscription;
                    if ($sub && $sub->isActive()) {
                        $unlockedProfiles[] = $profile->type;
                    }
                }
                $unlockedProfiles = array_values(array_unique($unlockedProfiles));

                // 3. Calculate current_profile (Dynamic)
                $activeProfile = $user->profiles->firstWhere('is_active', true);
                $currentProfileName = $activeProfile ? $activeProfile->type : $firstProfileName;

                // Check profile switch requests. If target of switch is unlocked, update current_profile
                $switchRequests = \App\Models\ProfileSwitchRequest::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($switchRequests as $request) {
                    if (in_array($request->to_profile_type, $unlockedProfiles)) {
                        $currentProfileName = $request->to_profile_type;
                        break;
                    }
                }

                $config = [
                    'user_profile' => [
                        'user_id' => $user->uuid,
                        'full_name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'profile_photo_url' => $user->profile_photo_url,
                        'bio' => $user->bio,
                        'gender' => $user->gender,
                        'language_preference' => $user->language_preference,
                        'is_verified' => (bool) $user->is_verified,
                        'followers_count' => (int) $user->followers_count,
                        'following_count' => (int) $user->following_count,
                        'posts_count' => (int) $user->posts_count,
                        'reputation_score' => (int) $user->reputation_score,
                        'created_at' => $user->created_at?->toIso8601String(),
                        'last_login_at' => $user->last_login_at?->toIso8601String(),
                        'first_profile' => $firstProfileName,
                        'current_profile' => $currentProfileName,
                        'unlocked_profiles' => $unlockedProfiles,
                    ],
                ];

                foreach ($user->profiles as $profile) {
                    $section = match ($profile->type) {
                        'employer' => $this->buildEmployerSection($profile),
                        'seller' => $this->buildEcommerceSection($profile, $user->id),
                        'music' => $this->buildMusicSection($profile),
                        'creator' => $this->buildCreatorSection($profile, $user),
                        'personal' => $this->buildPersonalSection($profile),
                        default => null,
                    };

                    if ($section !== null) {
                        $key = match ($profile->type) {
                            'employer' => 'employer',
                            'seller' => 'ecommerce',
                            'music' => 'music_play',
                            'creator' => 'content_creation',
                            'personal' => 'personal_profile',
                        };
                        $config[$key] = $section;
                    }
                }

                return $config;
            } catch (Throwable $e) {
                Log::error('Failed to build profile config', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    public function forgetCache(int $userId): void
    {
        Cache::forget("profile_config:{$userId}");
    }

    private function buildEmployerSection($profile): array
    {
        $details = $profile->employerDetails;
        $subscription = $profile->activeSubscription;

        return [
            'profile_id' => $profile->id,
            'is_active' => (bool) $profile->is_active,
            'company_name' => $details?->company_name,
            'industry' => $details?->industry,
            'company_size' => $details?->company_size,
            'company_website' => $details?->company_website,
            'company_phone' => $details?->company_phone,
            'company_address' => $details?->company_address,
            'subscription' => $this->formatSubscription($subscription),
        ];
    }

    private function buildEcommerceSection($profile, int $userId): array
    {
        $details = $profile->sellerDetails;
        $subscription = $profile->activeSubscription;

        $totalProducts = UserProduct::where('seller_id', $userId)->count();
        $featuredProduct = UserProduct::where('seller_id', $userId)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->select(['id', 'uuid', 'title', 'price', 'stock', 'seller_id', 'status', 'created_at'])
            ->first();

        $totalServices = UserService::where('seller_id', $userId)->count();
        $activeServices = UserService::where('seller_id', $userId)
            ->where('status', 'active')
            ->limit(5)
            ->select(['id', 'uuid', 'title', 'price', 'seller_id', 'status'])
            ->get();

        return [
            'profile_id' => $profile->id,
            'is_active' => (bool) $profile->is_active,
            'type' => $details?->seller_type,
            'seller_type_label' => $details?->seller_type === 'both'
                ? 'Products & Services'
                : ucfirst($details?->seller_type ?? 'products'),
            'product' => [
                'enabled' => $details?->sellsProducts() ?? false,
                'total_products' => $totalProducts,
                'featured_product' => $featuredProduct ? [
                    'product_id' => $featuredProduct->uuid,
                    'name' => $featuredProduct->title,
                    'price' => (float) $featuredProduct->price,
                    'currency' => 'INR',
                    'stock' => (int) $featuredProduct->stock,
                ] : null,
            ],
            'service' => [
                'enabled' => $details?->sellsServices() ?? false,
                'total_services' => $totalServices,
                'active_services' => $activeServices->map(fn($s) => [
                    'service_id' => $s->uuid,
                    'name' => $s->title,
                    'price' => (float) $s->price,
                    'currency' => 'INR',
                ])->toArray(),
            ],
            'subscription' => $this->formatSubscription($subscription),
        ];
    }

    private function buildMusicSection($profile): array
    {
        $details = $profile->musicDetails;

        return [
            'profile_id' => $profile->id,
            'is_active' => (bool) $profile->is_active,
            'artist_name' => $details?->artist_name,
            'stage_name' => $details?->stage_name,
            'genre' => $details?->genre,
            'label' => $details?->label,
            'bio' => $details?->bio,
            'current_track' => null,
            'playback_status' => 'stopped',
            'volume' => 50,
            'subscription' => [
                'is_active' => false,
                'plan_name' => 'Not Subscribed',
                'plan_slug' => null,
                'price' => 0,
                'currency' => 'INR',
                'duration_days' => 0,
                'features' => [],
                'start_date' => null,
                'expiry_date' => null,
                'next_billing_date' => null,
                'auto_renew' => false,
            ],
        ];
    }

    private function buildCreatorSection($profile, User $user): array
    {
        $details = $profile->creatorDetails;
        $subscription = $profile->activeSubscription;

        return [
            'profile_id' => $profile->id,
            'is_active' => (bool) $profile->is_active,
            'channel_name' => $details?->channel_name,
            'content_category' => $details?->content_category,
            'platform' => $details?->platform,
            'bio' => $details?->bio,
            'subscribers_count' => (int) $user->followers_count,
            'subscription_details' => $this->formatSubscription($subscription),
        ];
    }

    private function buildPersonalSection($profile): array
    {
        $subscription = $profile->activeSubscription;

        return [
            'profile_id' => $profile->id,
            'is_active' => (bool) $profile->is_active,
            'subscription' => $this->formatSubscription($subscription),
        ];
    }

    private function formatSubscription($subscription): array
    {
        if (!$subscription || !$subscription->plan) {
            return [
                'is_active' => false,
                'plan_name' => 'No Plan',
                'plan_slug' => null,
                'price' => 0,
                'currency' => 'INR',
                'duration_days' => 0,
                'billing_cycle' => null,
                'features' => [],
                'start_date' => null,
                'expiry_date' => null,
                'next_billing_date' => null,
                'auto_renew' => false,
            ];
        }

        $plan = $subscription->plan;

        return [
            'is_active' => $subscription->isActive(),
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'price' => (float) $plan->price,
            'currency' => $plan->currency,
            'duration_days' => (int) $plan->duration_days,
            'billing_cycle' => $this->deriveBillingCycle($plan->duration_days),
            'features' => $plan->features ?? [],
            'start_date' => $subscription->starts_at?->toIso8601String(),
            'expiry_date' => $subscription->ends_at?->toIso8601String(),
            'next_billing_date' => $subscription->next_billing_at?->toIso8601String(),
            'auto_renew' => (bool) $subscription->auto_renew,
        ];
    }

    private function deriveBillingCycle(int $durationDays): string
    {
        return match (true) {
            $durationDays >= 365 => 'yearly',
            $durationDays >= 90  => 'quarterly',
            $durationDays >= 30  => 'monthly',
            $durationDays >= 7   => 'weekly',
            default              => 'daily',
        };
    }
}
