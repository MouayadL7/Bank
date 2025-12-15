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
}
