<?php

namespace Modules\Account\Patterns\States;

use Modules\Account\Models\Account;

abstract class BaseAccountState implements AccountStateInterface
{
    abstract public function name(): string;

    public function deposit(Account $account, float $amount): void
    {
        $account->balance += $amount;
    }

    public function withdraw(Account $account, float $amount): void
    {
        if ($account->balance < $amount) {
            throw new \DomainException("Insufficient funds");
        }

        $account->balance -= $amount;
    }

    public function transitionTo(Account $account, string $newState): void
    {
        $account->state = $newState;
    }
}
