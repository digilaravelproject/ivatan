<?php

namespace App\Services;

use App\Models\PostReport;
use App\Models\UserPost;
use App\Models\User;

class ReportService
{
    public function report(UserPost $post, User $user, string $reason, ?string $description = null): PostReport
    {
        return PostReport::create([
            'post_id'     => $post->id,
            'user_id'     => $user->id,
            'reason'      => $reason,
            'description' => $description,
        ]);
    }

    public function reportCount(UserPost $post): int
    {
        return $post->reports()->count();
    }
}
