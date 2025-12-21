<?php

namespace Modules\Transaction\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT    = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case TRANSFER   = 'transfer';

    /**
     * Get all enum values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * English label
     */
    public function labelEN(): string
    {
        return match ($this) {
            self::DEPOSIT    => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
            self::TRANSFER   => 'Transfer',
        };
    }

    /**
     * Arabic label
     */
    public function labelAR(): string
    {
        return match ($this) {
            self::DEPOSIT    => 'إيداع',
            self::WITHDRAWAL => 'سحب',
            self::TRANSFER   => 'تحويل',
        };
    }

    /**
     * Get enum from Arabic label
     */
    public static function fromLabelAR(string $label): ?self
    {
        return match ($label) {
            'إيداع'   => self::DEPOSIT,
            'سحب'     => self::WITHDRAWAL,
            'تحويل'   => self::TRANSFER,
            default   => null,
        };
    }
}
