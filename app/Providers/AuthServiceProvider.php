<?php

namespace App\Providers;

use App\Models\UserPost;
use App\Policies\PostPolicy;
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
    // public function boot(): void
    // {
    //     // $this->registerPolicies();

    //     // // Optional: Define a Gate for admin-only actions
    //     // Gate::define('admin-actions', function ($user) {
    //     //     return $user->hasRole('admin'); // Spatie permission method
    //     // });
    // }
    public function boot()
{ $this->registerPolicies();
    Gate::define('admin-actions', function ($user) {
    return $user->hasRole('admin');
});


}

}
