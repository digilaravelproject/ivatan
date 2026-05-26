<?php

namespace Database\Seeders;

use App\Models\AdPackage;
use App\Models\Interest;
use App\Models\InterestCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Roles ─────────────────────────────────────────────
        $roles = ['admin', 'moderator', 'user'];
        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ─── 2. Permissions ───────────────────────────────────────
        $permission = Permission::firstOrCreate([
            'name' => 'view all orders',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo($permission);

        // ─── 3. Admin User ────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Super Admin',
                'password' => bcrypt('12345678'),
                'is_admin' => true,
            ]
        );
        $admin->assignRole('admin');

        // ─── 4. Dummy Users (for testing) ─────────────────────────
        $dummyUsers = [
            ['name' => 'Test User One',  'email' => 'user1@test.com'],
            ['name' => 'Test User Two',  'email' => 'user2@test.com'],
        ];

        foreach ($dummyUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('user');
        }

        // ─── 5. Interest Categories & Interests ───────────────────
        $categories = [
            'Technology' => [
                'Web Development', 'Software Engineering', 'AI / Machine Learning',
                'Mobile Apps', 'Cybersecurity', 'Gaming / eSports',
            ],
            'Business & Finance' => [
                'Startups & Entrepreneurship', 'Investing & Stock Market',
                'Personal Finance', 'Real Estate', 'Marketing & Sales',
                'Crypto / Blockchain',
            ],
            'Jobs & Careers' => [
                'Quick Hiring / Part-time', 'Freelancing / Remote Work',
                'Corporate Jobs', 'Government Jobs', 'Skill Development',
            ],
            'Education & Learning' => [
                'Coding / IT', 'Business & Management', 'Language Learning',
                'Competitive Exams', 'Science & Research',
            ],
            'Entertainment & Lifestyle' => [
                'Movies & Series', 'Music',
            ],
        ];

        foreach ($categories as $categoryName => $interests) {
            $category = InterestCategory::firstOrCreate(['name' => $categoryName]);

            foreach ($interests as $item) {
                Interest::firstOrCreate([
                    'name' => $item,
                    'interest_category_id' => $category->id,
                ]);
            }
        }

        // ─── 6. Default Ad Packages ───────────────────────────────
        $packages = [
            [
                'name' => 'Basic',
                'price' => 0,
                'duration_days' => 7,
                'reach_limit' => 1000,
                'description' => 'Free basic ad package to get started',
            ],
            [
                'name' => 'Standard',
                'price' => 499,
                'duration_days' => 14,
                'reach_limit' => 5000,
                'description' => 'Standard ad package for growing reach',
            ],
            [
                'name' => 'Premium',
                'price' => 999,
                'duration_days' => 30,
                'reach_limit' => 15000,
                'description' => 'Premium ad package for maximum visibility',
            ],
        ];

        foreach ($packages as $pkg) {
            AdPackage::firstOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }

        $this->command->info('System database seeded successfully!');
        $this->command->warn('Admin: admin@admin.com / 12345678 — CHANGE AFTER LOGIN!');
        $this->command->info('Dummy users: user1@test.com / password, user2@test.com / password');
    }
}
