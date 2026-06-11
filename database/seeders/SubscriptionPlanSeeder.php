<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // Seller Plans (3)
            [
                'profile_type' => 'seller',
                'name' => 'Basic Seller',
                'slug' => 'seller-basic',
                'description' => 'Start selling with basic features. Sell products OR services.',
                'price' => 0,
                'duration_days' => 36500,
                'features' => ['List up to 10 products', 'Basic analytics', 'Standard support'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'profile_type' => 'seller',
                'name' => 'Pro Seller',
                'slug' => 'seller-pro',
                'description' => 'Sell both products AND services with advanced features.',
                'price' => 499.00,
                'duration_days' => 30,
                'features' => ['Unlimited products', 'Unlimited services', 'Sell both types', 'Advanced analytics', 'Priority support', 'Featured listings'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'profile_type' => 'seller',
                'name' => 'Enterprise Seller',
                'slug' => 'seller-enterprise',
                'description' => 'Complete business solution for high-volume sellers.',
                'price' => 1499.00,
                'duration_days' => 90,
                'features' => ['Everything in Pro', 'Bulk product upload', 'Dedicated account manager', 'API access', 'Custom storefront', 'Advanced reporting'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            // Content Creator Plans (2)
            [
                'profile_type' => 'creator',
                'name' => 'Basic Creator',
                'slug' => 'creator-basic',
                'description' => 'Start creating and sharing content.',
                'price' => 0,
                'duration_days' => 36500,
                'features' => ['Upload content', 'Basic dashboard', 'Standard analytics'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'profile_type' => 'creator',
                'name' => 'Pro Creator',
                'slug' => 'creator-pro',
                'description' => 'Monetize your content with premium features.',
                'price' => 799.00,
                'duration_days' => 30,
                'features' => ['Unlimited uploads', 'Monetization tools', 'Advanced analytics', 'Priority support', 'Content scheduling', 'Collaboration tools'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            // Employer Plans (2)
            [
                'profile_type' => 'employer',
                'name' => 'Basic Employer',
                'slug' => 'employer-basic',
                'description' => 'Start hiring with basic features. Post limited jobs.',
                'price' => 0,
                'duration_days' => 36500,
                'features' => ['Post up to 3 jobs', 'Basic candidate search', 'Standard support'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'profile_type' => 'employer',
                'name' => 'Pro Employer',
                'slug' => 'employer-pro',
                'description' => 'Advanced tools for professional recruitment.',
                'price' => 999.00,
                'duration_days' => 30,
                'features' => ['Unlimited job posts', 'Advanced candidate filtering', 'Priority applicant support', 'Resume downloads', 'Premium badge'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            // Music Plans (2)
            [
                'profile_type' => 'music',
                'name' => 'Basic Music',
                'slug' => 'music-basic',
                'description' => 'Share and play music with standard quality.',
                'price' => 0,
                'duration_days' => 36500,
                'features' => ['Create playlists', 'Standard streaming quality', 'List up to 5 tracks'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'profile_type' => 'music',
                'name' => 'Premium Music',
                'slug' => 'music-premium',
                'description' => 'High-fidelity audio playback and unlimited uploads.',
                'price' => 199.00,
                'duration_days' => 30,
                'features' => ['Ad-free listening', 'Ultra-HQ audio streaming', 'Unlimited track uploads', 'Offline playback', 'Exclusive artist badge'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            // Personal Plans (5)
            [
                'profile_type' => 'personal',
                'name' => 'Free',
                'slug' => 'personal-free',
                'description' => 'Basic personal profile with essential features.',
                'price' => 0,
                'duration_days' => 36500,
                'features' => ['Basic profile', 'Standard support'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Personal Basic',
                'slug' => 'personal-basic',
                'description' => 'Enhanced personal features.',
                'price' => 99.00,
                'duration_days' => 30,
                'features' => ['Profile customization', 'Priority support', 'Extended storage'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Personal Pro',
                'slug' => 'personal-pro',
                'description' => 'Professional personal experience.',
                'price' => 299.00,
                'duration_days' => 30,
                'features' => ['All Basic features', 'Advanced analytics', 'Badge', 'Early access to features'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Personal Premium',
                'slug' => 'personal-premium',
                'description' => 'Premium personal account with all benefits.',
                'price' => 599.00,
                'duration_days' => 30,
                'features' => ['All Pro features', 'Premium badge', 'VIP support', 'Exclusive content access'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Personal Lifetime',
                'slug' => 'personal-lifetime',
                'description' => 'One-time payment for lifetime premium access.',
                'price' => 4999.00,
                'duration_days' => 36500,
                'features' => ['All Premium features', 'Lifetime badge', 'Lifetime VIP support', 'All future updates'],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::withoutEvents(fn() =>
                SubscriptionPlan::updateOrCreate(
                    ['slug' => $plan['slug']],
                    $plan
                )
            );
        }
    }
}
