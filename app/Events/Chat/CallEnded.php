<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $peerId;
    public string $uuid;

    public function __construct(int $peerId, string $uuid)
    {
        $this->peerId = $peerId;
        $this->uuid = $uuid;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->peerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.ended';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'status' => 'ended',
        ];
    }
}
