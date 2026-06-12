<?php

namespace App\Jobs;

use App\Events\Chat\CallTimeout;
use App\Models\User;
use App\Models\UserCallSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupMissedCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $callUuid;
    public $tries = 3;
    public $backoff = [1, 5, 10];

    public function __construct(string $callUuid)
    {
        $this->callUuid = $callUuid;
    }

    public function handle(): void
    {
        try {
            $session = UserCallSession::where('uuid', $this->callUuid)
                ->where('status', 'ringing')
                ->first();

            if (!$session) return;

            $session->update([
                'status' => 'missed',
                'ended_at' => now(),
            ]);

            broadcast(new CallTimeout($session->caller_id, $session->uuid))->toOthers();

            User::where('id', $session->caller_id)->update([
                'is_busy' => false,
                'busy_status' => null,
            ]);

            if ($session->receiver_id) {
                User::where('id', $session->receiver_id)->update([
                    'is_busy' => false,
                    'busy_status' => null,
                ]);
            }

            Log::info('CleanupMissedCallJob: Call marked as missed', [
                'uuid' => $this->callUuid,
                'caller_id' => $session->caller_id,
                'receiver_id' => $session->receiver_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('CleanupMissedCallJob failed', [
                'uuid' => $this->callUuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(?\Throwable $exception = null): void
    {
        Log::error('CleanupMissedCallJob completely failed', [
            'uuid' => $this->callUuid,
            'error' => $exception?->getMessage(),
        ]);
    }
}
