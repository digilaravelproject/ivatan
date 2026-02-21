<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'caption' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['image', 'video', 'carousel']),
            'media_metadata' => json_encode([
                'url' => $this->faker->imageUrl(),
                'size' => $this->faker->numberBetween(100, 5000),
            ]),
            'status' => $this->faker->randomElement(['active', 'deleted', 'flagged']),
            'visibility' => $this->faker->randomElement(['public', 'private', 'friends']),
        ];
    }
}
