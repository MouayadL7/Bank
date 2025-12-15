<?php

namespace Modules\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Repositories\Interfaces\SharedRepositoryInterface;
use Modules\Shared\Repositories\Eloquent\SharedRepository;

class SharedRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            SharedRepositoryInterface::class,
            SharedRepository::class
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
