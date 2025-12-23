<?php

namespace Modules\Account\Enums;

enum AccountType: string
{
    case SAVINGS    = 'savings';
    case CHECKING   = 'checking';
    case LOAN       = 'loan';
    case INVESTMENT = 'investment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            AccountType::SAVINGS => 'Savings Account',
            AccountType::CHECKING => 'Checking Account',
            AccountType::LOAN => 'Loan Account',
            AccountType::INVESTMENT => 'Investment Account',
        };
    }

    public function decoratorClass(): string
    {
        return match ($this) {
            self::SAVINGS => \Modules\Accounts\Decorators\SavingsAccountDecorator::class,
            self::CHECKING => \Modules\Accounts\Decorators\CheckingAccountDecorator::class,
            self::LOAN => \Modules\Accounts\Decorators\LoanAccountDecorator::class,
            self::INVESTMENT => \Modules\Accounts\Decorators\InvestmentAccountDecorator::class,
        };
    }

    public function isLoan(): bool
    {
        return $this === self::LOAN;
    }
}
