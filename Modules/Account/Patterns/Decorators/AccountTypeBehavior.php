<?php

namespace Modules\Account\Decorators;

use Modules\Account\Models\Account;

interface AccountTypeBehavior
{
    public function calculateBalance(Account $account): float;

    public function onDeposit(Account $account, float $amount): void;

    public function onWithdraw(Account $account, float $amount): void;
}
