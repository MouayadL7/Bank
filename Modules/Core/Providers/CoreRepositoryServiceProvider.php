<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Repositories\Interfaces\CoreRepositoryInterface;
use Modules\Core\Repositories\Eloquent\CoreRepository;

class CoreRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            CoreRepositoryInterface::class,
            CoreRepository::class
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
