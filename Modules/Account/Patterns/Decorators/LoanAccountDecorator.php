<?php

namespace Modules\Account\Decorators;

class LoanAccountDecorator extends AccountTypeDecorator
{
    public function onDeposit($account, float $amount): void
    {
        // deposit = payment
    }

    public function onWithdraw($account, float $amount): void
    {
        throw new \DomainException('Withdraw not allowed on loan account');
    }
}
