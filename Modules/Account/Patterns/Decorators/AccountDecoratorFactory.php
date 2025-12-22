<?php

namespace Modules\Account\Decorators;

use Modules\Account\Models\Account;

class AccountDecoratorFactory
{
    public static function make(Account $account): AccountTypeDecorator
    {
        return match ($account->type) {
            'savings' => new SavingsAccountDecorator($account),
            'checking' => new CheckingAccountDecorator($account),
            'loan' => new LoanAccountDecorator($account),
            'investment' => new InvestmentAccountDecorator($account),
            default => throw new \DomainException('Unknown account type'),
        };
    }
}
