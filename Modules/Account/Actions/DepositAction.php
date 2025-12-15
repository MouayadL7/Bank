<?php

namespace Modules\Account\Actions;

use Modules\Account\Models\Account;

class DepositAction
{
    public function execute(Account $account, float $amount): Account
    {
        $account->balance += $amount;
        return $account;
    }
}

