<?php

namespace App\Listeners;

use App\Services\PresenceService;
use Illuminate\Auth\Events\Logout;

class SetUserOffline
{
    protected PresenceService $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    public function handle(Logout $event): void
    {
        $user = $event->user;
        if ($user) {
            $this->presenceService->setOffline($user);
        }
    }
}
