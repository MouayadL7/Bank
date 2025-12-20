<?php

namespace Modules\User\Enums;

enum UserStatus: string
{
    case ACTIVE    = 'active';
    case SUSPENDED = 'suspended';
    case DISABLED  = 'disabled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
