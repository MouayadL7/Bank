<?php

namespace Modules\Account\Actions;

use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Account\Models\Account;

class TransferAction
{
    private WithdrawAction $withdrawAction;
    private DepositAction $depositAction;

    public function __construct(WithdrawAction $withdrawAction, DepositAction $depositAction)
    {
        $this->withdrawAction = $withdrawAction;
        $this->depositAction = $depositAction;
    }

    /**
     * Execute the transfer between two accounts.
     *
     * @param Account $from
     * @param Account $to
     * @param float $amount
     * @return void
     */
    public function execute(Account $from, Account $to, float $amount)
    {
        event(new AccountBalanceUpdated(
            $from,
            -$amount,
            'transfer',
            $from->id,
            $to->id
        ));

        event(new AccountBalanceUpdated(
            $to,
            $amount,
            'transfer',
            $from->id,
            $to->id
        ));
    }
}


