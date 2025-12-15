<?php

namespace Modules\Account\Enums;

enum AccountState: string
{
    case ACTIVE    = 'active';
    case FROZEN    = 'frozen';
    case SUSPENDED = 'suspended';
    case CLOSED    = 'closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::FROZEN => 'Frozen',
            self::SUSPENDED => 'Suspended',
            self::CLOSED => 'Closed',
        };
    }
}
