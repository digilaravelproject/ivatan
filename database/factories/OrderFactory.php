<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'buyer_id' => User::factory(),
            'total_amount' => 0, // Will be updated after items are created
            'shipping_address' => [
                'line1' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'state' => $this->faker->state,
                'postal_code' => $this->faker->postcode,
                'country' => $this->faker->country,
            ],
            'status' => $this->faker->randomElement([
                'pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'
            ]),
            'placed_at' => Carbon::now()->subDays(rand(1, 30)),
        ];
    }
}
