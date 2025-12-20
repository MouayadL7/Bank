<?php

namespace Modules\Account\Enums;

use Modules\Account\Patterns\States\{
    AccountStateInterface,
    ActiveState,
    FrozenState,
    SuspendedState,
    ClosedState
};

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

    public function resolve(): AccountStateInterface
    {
        return match ($this) {
            self::ACTIVE => new ActiveState(),
            self::FROZEN => new FrozenState(),
            self::SUSPENDED => new SuspendedState(),
            self::CLOSED => new ClosedState(),
        };
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
