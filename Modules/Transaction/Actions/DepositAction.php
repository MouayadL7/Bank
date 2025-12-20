<?php

namespace Modules\Transaction\Actions;

use Modules\Account\Factories\AccountComponentFactory;
use Modules\Account\Models\Account;

class DepositAction
{
    public function execute(Account $account, float $amount): Account
    {
        $component = AccountComponentFactory::make($account);
        $component->deposit($amount);

        return $account;
    }
}



