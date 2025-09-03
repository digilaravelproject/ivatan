<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['image', 'video']);

        return [
            'user_id' => User::factory(),
            'media_url' => $type === 'image'
                ? $this->faker->imageUrl(1080, 1920, 'people', true)
                : $this->faker->url(), // assuming video URL
            'type' => $type,
            'expires_at' => Carbon::now()->addHours(24),
            'is_archived' => $this->faker->boolean(10), // 10% archived
        ];
    }
}
