<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\UpdateUnreadNotificationCount;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationSent::class => [
            UpdateUnreadNotificationCount::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
