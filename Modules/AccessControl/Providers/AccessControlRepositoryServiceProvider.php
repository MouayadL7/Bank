<?php

namespace Modules\AccessControl\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\AccessControl\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\AccessControl\Repositories\Eloquent\RoleRepository;

class AccessControlRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
