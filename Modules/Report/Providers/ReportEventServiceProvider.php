<?php

namespace Modules\Report\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Account\Events\AccountClosed;
use Modules\Account\Events\AccountStateChanged;
use Modules\Report\Listeners\LogAccountBalanceChange;
use Modules\Report\Listeners\LogAccountClosed;
use Modules\Report\Listeners\LogAccountStateChange;

class ReportEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AccountBalanceUpdated::class => [
            LogAccountBalanceChange::class,
        ],
        AccountStateChanged::class => [
            LogAccountStateChange::class,
        ],
        AccountClosed::class => [
            LogAccountClosed::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

