<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional()->numerify('03#########'),

            'email_verified_at' => now(),
            'is_verified' => $this->faker->boolean(80),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            // 'profile_photo_path' => $this->faker->imageUrl(300, 300, 'people', true, 'User'),
            'bio' => $this->faker->optional()->sentence(10),
            'followers_count' => $this->faker->numberBetween(0, 1000),
            'following_count' => $this->faker->numberBetween(0, 1000),
            'posts_count' => $this->faker->numberBetween(0, 200),
            'is_blocked' => $this->faker->boolean(5),

            'settings' => json_encode([
                'dark_mode' => $this->faker->boolean(),
                'language' => $this->faker->randomElement(['en', 'ur', 'hi']),
                'notifications' => $this->faker->boolean(90),
            ]),

            'last_login_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
            'is_verified' => false,
        ]);
    }
     public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = Role::firstOrCreate(['name' => 'user']);
            $user->assignRole($role);
        });
    }
}
