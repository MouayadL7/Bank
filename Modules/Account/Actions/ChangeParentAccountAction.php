<?php

namespace Modules\Account\Actions;

use Modules\Account\Models\Account;
use DomainException;
use Modules\Account\Enums\AccountState;

class ChangeParentAccountAction
{
    public function execute(Account $account, ?Account $newParent): void
    {
        if ($account->state === AccountState::CLOSED) {
            throw new DomainException('Cannot move a closed account.');
        }

        if ($newParent && $newParent->id === $account->id) {
            throw new DomainException('Account cannot be parent of itself.');
        }

        $account->parent_account_id = $newParent?->id;
    }
}

