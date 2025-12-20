<?php

namespace Modules\Account\Patterns\States;

use DomainException;
use Modules\Account\Enums\AccountState;
use Modules\Account\Models\Account;

class SuspendedState extends BaseAccountState
{
    public function name(): string
    {
        return AccountState::SUSPENDED->value;
    }

    public function deposit(Account $account, float $amount): void
    {
        throw new DomainException("Account is suspended. Deposits are not allowed.");
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new DomainException("Account is suspended. Withdrawals are not allowed.");
    }

    public function transitionTo(Account $account, string $newState): void
    {
        $target = AccountState::from($newState);

        if ($target !== AccountState::ACTIVE) {
            throw new DomainException("Suspended account can only be reactivated");
        }

        $account->state = $target;
    }
}
