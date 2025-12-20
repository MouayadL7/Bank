<?php

namespace Modules\Account\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Account\Models\Account;

class AccountClosed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Account $account) {}
}
