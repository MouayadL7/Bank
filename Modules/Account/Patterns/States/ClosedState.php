<?php

namespace Modules\Account\Patterns\States;

use Modules\Account\Enums\AccountState;
use Modules\Account\Models\Account;

class ClosedState extends BaseAccountState
{
    public function name(): string
    {
        return AccountState::CLOSED->value;
    }

    public function deposit(Account $account, float $amount): void
    {
        throw new \DomainException("Account is closed. Cannot deposit.");
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new \DomainException("Account is closed. Cannot withdraw.");
    }

    public function transitionTo(Account $account, string $newState): void
    {
        throw new \DomainException("Closed accounts cannot change state.");
    }
}
