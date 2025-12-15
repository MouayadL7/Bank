<?php

namespace Modules\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Repositories\Eloquent\AccountRepository;

class AccountRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            AccountRepositoryInterface::class,
            AccountRepository::class
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
