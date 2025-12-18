<?php

namespace Modules\Account\Patterns\States;

use DomainException;
use Modules\Account\Enums\AccountState;
use Modules\Account\Models\Account;

class FrozenState extends BaseAccountState
{
    public function name(): string
    {
        return AccountState::FROZEN->value;
    }

    public function deposit(Account $account, float $amount): void
    {
        throw new DomainException("Account is frozen. Deposits are not allowed.");
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new DomainException("Account is frozen. Withdrawals are not allowed.");
    }

    public function transitionTo(Account $account, string $newState): void
    {
        $target = AccountState::from($newState);

        if ($target !== AccountState::ACTIVE) {
            throw new DomainException("Frozen account can only be reactivated");
        }

        $account->state = $target;
    }
}
