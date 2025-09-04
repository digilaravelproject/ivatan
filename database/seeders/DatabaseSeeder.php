<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Story;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Job;
use App\Models\Report;

class DatabaseSeeder extends Seeder
{
     // Call RoleSeeder first to create roles and admin user
     /**
      * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(RoleSeeder::class);
        // Create some users (including admin)
        $userCollection = User::factory()->count(10)->create();

        $userCollection->each(function ($user) {
            $user->assignRole('user');
        });

        // Posts
        Post::factory()->count(50)->create();

        // Reels
        Reel::factory()->count(20)->create();

        // Stories
        Story::factory()->count(15)->create();

        // Products
        Product::factory()->count(30)->create();

        // Orders with items
        Order::factory()
            ->count(10)
            ->create()
            ->each(function ($order) {
                $items = OrderItem::factory()->count(rand(1, 5))->create([
                    'order_id' => $order->id,
                ]);

                // Update total_amount based on item subtotals
                $order->update([
                    'total_amount' => $items->sum('subtotal'),
                ]);
            });

        // Jobs
        Job::factory()->count(5)->create();

        // Reports
        Report::factory()->count(8)->create();
    }
}


// namespace Database\Seeders;

// use App\Models\User;
// // use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     /**
//      * Seed the application's database.
//      */
//     public function run(): void
//     {
//         // User::factory(10)->create();

//         User::factory()->create([
//             'name' => 'Test User',
//             'email' => 'test@example.com',
//         ]);
//     }
// }
