<?php

namespace Modules\Transaction\Enums;

enum TransactionFrequencyEnum: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
