<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserCallSession;
use Illuminate\Console\Command;

class CleanupStalePresence extends Command
{
    protected $signature = 'presence:cleanup-stale';
    protected $description = 'Mark users offline if last_seen_at > 3 minutes ago and cancel stale ringing calls';

    public function handle(): void
    {
        $staleUsers = User::where('is_online', true)
            ->where('last_seen_at', '<', now()->subMinutes(3))
            ->get();

        if ($staleUsers->isEmpty()) {
            $this->info('No stale users found.');
            return;
        }

        $userIds = $staleUsers->pluck('id');

        UserCallSession::whereIn('caller_id', $userIds)
            ->where('status', 'ringing')
            ->update(['status' => 'missed', 'ended_at' => now()]);

        UserCallSession::whereIn('receiver_id', $userIds)
            ->where('status', 'ringing')
            ->update(['status' => 'missed', 'ended_at' => now()]);

        User::whereIn('id', $userIds)->update([
            'is_online' => false,
            'is_busy' => false,
            'busy_status' => null,
        ]);

        $this->info('Marked ' . $staleUsers->count() . ' users offline.');
    }
}
