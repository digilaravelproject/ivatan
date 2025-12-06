<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePostTrendingScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes max run time

    public function handle(): void
    {
        try {
            // Direct SQL Update (Super Fast Performance)
            // Hum wahi formula use kar rahe hain jo pehle banaya tha
            DB::statement('
                UPDATE user_posts
                SET trending_score = (
                    (
                        (view_count * 0.5) +
                        (like_count * 10) +
                        (comment_count * 20)
                    )
                    *
                    (CASE
                        WHEN type = "reel" THEN 1.5
                        WHEN type = "video" THEN 1.2
                        ELSE 1.0
                    END)
                    +
                    (CASE
                        WHEN created_at >= NOW() - INTERVAL 24 HOUR THEN 500
                        ELSE 0
                    END)
                )
                /
                POW((TIMESTAMPDIFF(HOUR, created_at, NOW()) + 2), 1.8)
                WHERE status = "active"
            ');

            Log::info("Trending scores updated successfully.");
        } catch (\Exception $e) {
            Log::error("Failed to update trending scores: " . $e->getMessage());
        }
    }
}
