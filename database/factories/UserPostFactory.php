<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserPostFactory extends Factory
{
    protected $model = UserPost::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['post', 'reel', 'video']),
            'caption' => $this->faker->sentence(),
            'like_count' => 0,
            'comment_count' => 0,
            'view_count' => 0,
            'status' => 'active',
            'visibility' => 'public',
        ];
    }

    public function reel(): static
    {
        return $this->state(fn(array $attrs) => ['type' => 'reel']);
    }

    public function video(): static
    {
        return $this->state(fn(array $attrs) => ['type' => 'video']);
    }

    public function post(): static
    {
        return $this->state(fn(array $attrs) => ['type' => 'post']);
    }
}
