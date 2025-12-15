<?php

namespace Modules\AccessControl\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\AccessControl\Repositories\Interfaces\AccessControlRepositoryInterface;
use Modules\AccessControl\Repositories\Eloquent\AccessControlRepository;

class AccessControlRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            AccessControlRepositoryInterface::class,
            AccessControlRepository::class
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
