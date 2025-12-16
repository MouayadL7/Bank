<?php
// app/Modules/Account/Events/AccountBalanceUpdated.php

namespace Modules\Account\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Account\Models\Account;

class AccountBalanceUpdated
{
    use SerializesModels;

    public $account;
    public $amount;
    public $transactionType;   
    public $fromAccountId;
    public $toAccountId;

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
    public function __construct(Account $account, float $amount, string $transactionType, ?int $fromAccountId = null, ?int $toAccountId = null)
    {
        $this->account = $account;
        $this->amount = $amount;
        $this->transactionType = $transactionType;
        $this->fromAccountId = $fromAccountId;
        $this->toAccountId = $toAccountId;
    }
}



