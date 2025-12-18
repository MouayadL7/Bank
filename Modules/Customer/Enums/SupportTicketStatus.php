<?php

namespace Modules\Customer\Enums;

enum SupportTicketStatus: string
{
    case OPEN        = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED    = 'resolved';

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
            self::OPEN        => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::RESOLVED    => 'Resolved',
        };
    }

    /**
     * Arabic label
     */
    public function labelAR(): string
    {
        return match ($this) {
            self::OPEN        => 'مفتوحة',
            self::IN_PROGRESS => 'قيد المعالجة',
            self::RESOLVED    => 'محلولة',
        };
    }

    /**
     * Get enum from Arabic label
     */
    public static function fromLabelAR(string $label): ?self
    {
        return match ($label) {
            'مفتوحة'        => self::OPEN,
            'قيد المعالجة'  => self::IN_PROGRESS,
            'محلولة'        => self::RESOLVED,
            default         => null,
        };
    }
}
