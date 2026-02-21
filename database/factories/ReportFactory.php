<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        // Simulate a report on a random model - for demo, we'll use Post
        $reportableType = 'App\Models\Post';
        $reportableId = \App\Models\Post::factory()->create()->id; // or pick an existing one

        $statuses = ['pending', 'in_review', 'resolved', 'dismissed'];
        $status = $this->faker->randomElement($statuses);

        $isResolved = in_array($status, ['resolved', 'dismissed']);
        $resolvedBy = $isResolved ? User::factory() : null;
        $resolvedAt = $isResolved ? Carbon::now()->subDays(rand(1, 10)) : null;

        return [
            'reported_by' => User::factory(),
            'reportable_type' => $reportableType,
            'reportable_id' => $reportableId,
            'reason' => $this->faker->sentence(4),
            'details' => $this->faker->optional()->paragraph,
            'status' => $status,
            'resolved_by' => $resolvedBy,
            'resolved_at' => $resolvedAt,
        ];
    }
}
