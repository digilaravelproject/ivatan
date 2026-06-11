<?php

namespace App\Listeners;

use App\Services\PresenceService;
use Illuminate\Auth\Events\Login;

class SetUserOnline
{
    protected PresenceService $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    public function handle(Login $event): void
    {
        $user = $event->user;
        $this->presenceService->setOnline($user);
    }
}
