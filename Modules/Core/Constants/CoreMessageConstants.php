<?php

declare(strict_types=1);

namespace Modules\Core\Constants;

use Illuminate\Support\Facades\Lang;

/**
 * Constants for Core module messages
 */
final class CoreMessageConstants
{
    // Generic Response Messages
    public const GENERIC_SUCCESS = 'core::messages.generic.success';

    public const GENERIC_ERROR = 'core::messages.generic.error';

    public const VALIDATION_FAILED = 'core::messages.generic.validation_failed';

    /**
     * Get a localized message
     *
     * @template TReplace of array
     *
     * @param  string  $key  The message key
     * @param  TReplace  $replace  The replacement values
     * @return string The localized message
     */
    public static function get(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace);
    }
}
