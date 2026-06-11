<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallAccepted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $callerId;
    public string $uuid;
    public array $sdpAnswer;

    public function __construct(int $callerId, string $uuid, array $sdpAnswer)
    {
        $this->callerId = $callerId;
        $this->uuid = $uuid;
        $this->sdpAnswer = $sdpAnswer;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->callerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.accepted';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'sdp_answer' => $this->sdpAnswer,
        ];
    }
}
