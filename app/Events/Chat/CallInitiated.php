<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $receiverId;
    public string $uuid;
    public array $callerInfo;
    public string $type;
    public array $sdpOffer;

    public function __construct(int $receiverId, string $uuid, array $callerInfo, string $type, array $sdpOffer)
    {
        $this->receiverId = $receiverId;
        $this->uuid = $uuid;
        $this->callerInfo = $callerInfo;
        $this->type = $type;
        $this->sdpOffer = $sdpOffer;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.initiated';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'caller_info' => $this->callerInfo,
            'type' => $this->type,
            'sdp_offer' => $this->sdpOffer,
        ];
    }
}
