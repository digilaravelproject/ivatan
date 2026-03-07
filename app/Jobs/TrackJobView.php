<?php

namespace App\Jobs;

use App\Models\Jobs\UserJobPost;
use App\Models\View;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackJobView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobPostId;
    public $userId;
    public $ipAddress;

    public function __construct($jobPostId, $userId, $ipAddress)
    {
        $this->jobPostId = $jobPostId;
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
    }

    public function handle()
    {
        $job = UserJobPost::find($this->jobPostId);
        if (!$job instanceof UserJobPost) return;

        $query = View::where('viewable_type', UserJobPost::class)
            ->where('viewable_id', $job->id);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        } else {
            $query->where('ip_address', $this->ipAddress);
        }

        $alreadyViewed = $query->exists();

        if (!$alreadyViewed) {
            View::create([
                'user_id' => $this->userId,
                'viewable_type' => UserJobPost::class,
                'viewable_id' => $job->id,
                'ip_address' => $this->ipAddress,
            ]);

            $job->increment('views_count');
        }
    }
}
