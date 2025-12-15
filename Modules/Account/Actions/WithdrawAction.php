<?php

namespace Modules\Account\Actions;

use Modules\Account\Models\Account;

class WithdrawAction
{
    public function execute(Account $account, float $amount): Account
    {
        if ($account->balance < $amount) {
            throw new \DomainException("Insufficient funds");
        }

        $account->balance -= $amount;

        return $account;
    }
}

