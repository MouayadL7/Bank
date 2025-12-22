<?php

namespace Modules\Account\Decorators;

use Modules\Account\Decorators\AccountTypeBehavior;
use Modules\Account\Models\Account;

abstract class AccountTypeDecorator implements AccountTypeBehavior
{
    public function __construct(
        protected Account $account
    ) {}

    public function calculateBalance(Account $account): float
    {
        return (float) $account->balance;
    }

    public function onDeposit(Account $account, float $amount): void
    {
        // default: no-op
    }

    public function onWithdraw(Account $account, float $amount): void
    {
        // default: no-op
    }
}
