<?php

namespace Modules\Account\Decorators;

use DomainException;

class SavingsAccountDecorator extends AccountTypeDecorator
{
    public function onWithdraw($account, float $amount): void
    {
        $account = $this->getModel();
        $minimum = (float) ($account->meta['minimum_balance'] ?? 0);

        if (($account->balance - $amount) < $minimum) {
            throw new DomainException("Minimum balance must be maintained");
        }

        parent::withdraw($amount);
    }
}
