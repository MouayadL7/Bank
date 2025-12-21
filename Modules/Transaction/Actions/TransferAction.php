<?php

namespace Modules\Transaction\Actions;

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
        $this->withdrawAction->execute($from, $amount);
        $this->depositAction->execute($to, $amount);
    }
}


