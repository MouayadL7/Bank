<?php

namespace Modules\Report\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;
use Modules\Report\Repositories\Eloquent\ReportRepository;

class ReportRepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReportRepositoryInterface::class,
            ReportRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}

