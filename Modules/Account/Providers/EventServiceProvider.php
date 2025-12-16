<?php

// app/Modules/Account/Providers/EventServiceProvider.php

namespace Modules\Account\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Account\Listeners\UpdateAccountBalance;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AccountBalanceUpdated::class => [
            UpdateAccountBalance::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}


