<?php

namespace Modules\AccessControl\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\AccessControl\Models\Role;
use Modules\User\Models\User;

class AccessControlAuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'Modules\AccessControl\Models\AccessControl' => 'Modules\AccessControl\Policies\AccessControlPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function (User $user) {
            return $user->role_id == Role::ROLE_ADMIN;
        });

        Gate::define('isManager', function (User $user) {
            return $user->role_id == Role::ROLE_MANAGER;
        });

        Gate::define('isTeller', function (User $user) {
            return $user->role_id == Role::ROLE_TELLER;
        });

        Gate::define('isCustomer', function (User $user) {
            return $user->role_id == Role::ROLE_CUSTOMER;
        });
    }
}
