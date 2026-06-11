<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IceCandidateExchange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $peerId;
    public string $uuid;
    public array $candidate;

    public function __construct(int $peerId, string $uuid, array $candidate)
    {
        $this->peerId = $peerId;
        $this->uuid = $uuid;
        $this->candidate = $candidate;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->peerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.ice_candidate';
    }

    public function broadcastWith(): array
    {
        return [
            'call_session_uuid' => $this->uuid,
            'candidate' => $this->candidate,
        ];
    }
}
