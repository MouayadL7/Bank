<?php

namespace Modules\Recurring\Listeners;

use Modules\Recurring\Events\DeactivateRecurringOnAccountSettled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Account\Events\AccountSettledEvent;
use Modules\Recurring\Models\Recurring;

class DeactivateRecurringOnAccountSettledListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccountSettledEvent $event): void
    {
        Recurring::where('account_uuid', $event->account->uuid)
            ->where('active', true)
            ->update([
                'active' => false,
            ]);
    }
}
