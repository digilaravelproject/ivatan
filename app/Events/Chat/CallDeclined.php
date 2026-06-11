<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallDeclined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $callerId;
    public string $uuid;
    public string $reason;

    public function __construct(int $callerId, string $uuid, string $reason = 'declined')
    {
        $this->callerId = $callerId;
        $this->uuid = $uuid;
        $this->reason = $reason;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->callerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.declined';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'reason' => $this->reason,
        ];
    }
}
