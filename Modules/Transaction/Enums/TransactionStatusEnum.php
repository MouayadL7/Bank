<?php

namespace App\Modules\Transaction\Enums;

enum TransactionStatus: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

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
            self::PENDING  => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Arabic label
     */
    public function labelAR(): string
    {
        return match ($this) {
            self::PENDING  => 'قيد المعالجة',
            self::APPROVED => 'مقبولة',
            self::REJECTED => 'مرفوضة',
        };
    }

    /**
     * Get enum from Arabic label
     */
    public static function fromLabelAR(string $label): ?self
    {
        return match ($label) {
            'قيد المعالجة' => self::PENDING,
            'مقبولة'        => self::APPROVED,
            'مرفوضة'        => self::REJECTED,
            default         => null,
        };
    }
}
