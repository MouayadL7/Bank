<?php

namespace Modules\Transaction\Actions;

use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Transaction\Models\Transaction;

class WithdrawAction
{
    public function execute(Transaction $transaction, float $amount): Transaction
    {
        if ($transaction->balance < $amount) {
            throw new \DomainException("Insufficient funds");
        }

        $transaction->balance -= $amount;
        event(new AccountBalanceUpdated(
            $transaction->account,
            -$amount,
            'withdraw' 
        ));

        return $transaction;
    }
}


