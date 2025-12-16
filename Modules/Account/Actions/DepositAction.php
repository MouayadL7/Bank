<?php
namespace Modules\Transaction\Actions;

use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Transaction\Models\Transaction;

class DepositAction
{
    public function execute(Transaction $transaction, float $amount): Transaction
    {
        $transaction->balance += $amount;

        event(new AccountBalanceUpdated(
            $transaction->account,
            $amount,
            'deposit'   
        ));

        return $transaction;
    }
}



