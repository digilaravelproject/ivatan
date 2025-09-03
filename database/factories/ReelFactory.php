<?php

namespace Database\Factories;

use App\Models\Reel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReelFactory extends Factory
{
    protected $model = Reel::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'user_id' => User::factory(), // creates a related user
            'video_url' => $this->faker->optional()->url,
            'cover_url' => $this->faker->optional()->url,
            'description' => $this->faker->optional()->paragraph,
            'duration_seconds' => $this->faker->optional()->numberBetween(5, 300),
            'status' => $this->faker->randomElement(['active', 'deleted', 'flagged']),
        ];
    }
}
