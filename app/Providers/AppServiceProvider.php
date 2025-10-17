<?php

namespace App\Providers;

use App\Models\UserPost;
use App\Observers\UserPostObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;


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
        ]);
        UserPost::observe(UserPostObserver::class);
    }
}
