<?php

namespace Modules\Account\Patterns\Composite;

use Modules\Account\Models\Account;

class SingleAccount implements AccountComponent
{
    public function __construct(private Account $account) {}

    public function getBalance(): float
    {
        return (float) $this->account->balance;
    }

    public function deposit(float $amount): void
    {
        $state = $this->account->getStateInstance();
        $state->deposit($this->account, $amount);
    }

    public function withdraw(float $amount): void
    {
        $state = $this->account->getStateInstance();
        $state->withdraw($this->account, $amount);
    }

    public function getModel(): Account
    {
        return $this->account;
    }
}
