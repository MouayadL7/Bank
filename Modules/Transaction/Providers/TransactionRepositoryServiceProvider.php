<?php

namespace Modules\Transaction\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Transaction\Repositories\Interfaces\TransactionRepositoryInterface;
use Modules\Transaction\Repositories\Eloquent\TransactionRepository;

class TransactionRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
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
