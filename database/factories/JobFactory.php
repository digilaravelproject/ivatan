<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $salaryFrom = $this->faker->numberBetween(30000, 80000);
        $salaryTo = $salaryFrom + $this->faker->numberBetween(5000, 40000);

        return [
            'uuid' => $this->faker->uuid,
            'posted_by' => User::factory(), // Job poster
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->optional()->paragraph(4),
            'company_name' => $this->faker->optional()->company,
            'location' => $this->faker->optional()->city,
            'salary_from' => $salaryFrom,
            'salary_to' => $salaryTo,
            'employment_type' => $this->faker->randomElement(['full-time','part-time','contract','freelance']),
            'status' => $this->faker->randomElement(['pending','approved','rejected','closed']),
        ];
    }
}
