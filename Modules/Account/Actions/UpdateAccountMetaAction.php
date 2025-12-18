<?php

namespace Modules\Account\Actions;

use Modules\Account\Models\Account;
use DomainException;

class UpdateAccountMetaAction
{
    public function execute(Account $account, array $meta): void
    {
        if ($account->state === 'closed') {
            throw new DomainException('Cannot modify a closed account.');
        }

        $account->meta = array_merge(
            $account->meta ?? [],
            $meta
        );
    }
}

