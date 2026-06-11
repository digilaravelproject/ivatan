<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserPost;
use App\Services\PresenceService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Broadcasting\PresenceChannelMemberLeft;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'UserPost' => UserPost::class,
            'App\\Models\\UserPost' => UserPost::class,
            'user_products' => \App\Models\Ecommerce\UserProduct::class,
            'user_services' => \App\Models\Ecommerce\UserService::class,
        ]);

        \Illuminate\Support\Facades\Event::listen(PresenceChannelMemberLeft::class, function ($event) {
            $member = $event->user ?? [];
            $userId = $member['id'] ?? null;
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    app(PresenceService::class)->setOffline($user);

                    \App\Models\UserCallSession::where(function ($q) use ($user) {
                        $q->where('caller_id', $user->id)->orWhere('receiver_id', $user->id);
                    })->whereIn('status', ['ringing'])->update([
                        'status' => 'missed',
                        'ended_at' => now(),
                    ]);
                }
            }
        });
    }
}
