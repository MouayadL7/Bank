<?php

namespace Modules\Transaction\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Account\Models\Account;

class AccountBalanceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fromAccount;
    public $toAccount;
    public $amount;
    public $transactionType;

    /**
     * Create a new event instance.
     *
     * @param Account $account
     * @param float $amount
     * @param string $transactionType
     * @param int|null $fromAccountId
     * @param int|null $toAccountId
     * @return void
     */
    public function __construct(Account $fromAccount, Account $toAccount, float $amount, string $transactionType)
    {
        $this->fromAccount = $fromAccount;
        $this->toAccount = $toAccount;
        $this->amount = $amount;
        $this->transactionType = $transactionType;
    }
}



