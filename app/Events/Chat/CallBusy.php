<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallBusy implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $callerId;
    public string $uuid;

    public function __construct(int $callerId, string $uuid)
    {
        $this->callerId = $callerId;
        $this->uuid = $uuid;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->callerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.busy';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'status' => 'busy',
        ];
    }
}
