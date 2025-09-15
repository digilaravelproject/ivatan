<?php

namespace App\Providers;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserService;
use App\Models\UserPost;
use App\Policies\PostPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        UserPost::class => PostPolicy::class,
        UserOrder::class => OrderPolicy::class,
        UserService::class => ServicePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You can bind services or helpers here if needed.
    }

    /**
     * Bootstrap any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('admin-actions', function ($user) {
            return $user->hasRole('admin');
        });
    }
}
