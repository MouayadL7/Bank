<?php

namespace Modules\Recurring\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Recurring\Repositories\Interfaces\RecurringRepositoryInterface;
use Modules\Recurring\Repositories\Eloquent\RecurringRepository;

class RecurringRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            RecurringRepositoryInterface::class,
            RecurringRepository::class
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
