<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallCancelled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $receiverId;
    public string $uuid;

    public function __construct(int $receiverId, string $uuid)
    {
        $this->receiverId = $receiverId;
        $this->uuid = $uuid;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.cancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'status' => 'cancelled',
        ];
    }
}
