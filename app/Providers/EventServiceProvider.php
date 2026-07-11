<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\SetUserOnline;
use App\Listeners\SetUserOffline;
use App\Listeners\UpdateUnreadNotificationCount;
use App\Models\UserPost;
use App\Observers\UserPostObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationSent::class => [
            UpdateUnreadNotificationCount::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
            SetUserOnline::class,
        ],
        Logout::class => [
            SetUserOffline::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        UserPost::observe(UserPostObserver::class);
    }
}
