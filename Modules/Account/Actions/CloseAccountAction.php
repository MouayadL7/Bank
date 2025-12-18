<?php

namespace Modules\Account\Actions;

use Modules\Account\Models\Account;
use DomainException;

class CloseAccountAction
{
    public function execute(Account $account): void
    {
        if ((float) $account->balance !== 0.0) {
            throw new DomainException('Account balance must be zero before closure.');
        }

        if ($account->children()->where('state', '!=', 'closed')->exists()) {
            throw new DomainException('All child accounts must be closed first.');
        }

        $state = $account->getStateInstance();
        $state->transitionTo($account, 'closed');
    }
}

