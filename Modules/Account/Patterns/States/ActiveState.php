<?php

namespace Modules\Account\Patterns\States;

use DomainException;
use Modules\Account\Enums\AccountState;
use Modules\Account\Models\Account;

class ActiveState extends BaseAccountState
{
    public function name(): string
    {
        return AccountState::ACTIVE->value;
    }

    public function transitionTo(Account $account, string $newState): void
    {
        $target = AccountState::from($newState);

        if (!in_array($target, [
            AccountState::FROZEN,
            AccountState::SUSPENDED,
        ])) {
            throw new DomainException("Invalid transition from active to {$newState}");
        }

        $account->state = $target;
    }
}
