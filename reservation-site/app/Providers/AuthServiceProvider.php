<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\User;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('is_admin_or_manager', function (User $user) {
            return $user->isAdmin() || $user->isManager();
        });

        Gate::define('is_expert', function (User $user) {
            return $user->isExpert();
        });

        Gate::define('is_customer', function (User $user) {
            return $user->isCustomer();
        });
    }
}
