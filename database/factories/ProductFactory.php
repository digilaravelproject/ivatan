<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true); // e.g. "Cool Leather Shoes"

        return [
            'uuid' => $this->faker->uuid,
            'user_id' => User::factory(), // assumes seller is a user
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5), // unique slug
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 100, 10000),
            'stock' => $this->faker->numberBetween(0, 100),
            'images' => json_encode([
                $this->faker->imageUrl(800, 800, 'product', true),
                $this->faker->imageUrl(800, 800, 'product', true),
            ]),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'deleted']),
        ];
    }
}
