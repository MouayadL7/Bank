<?php

declare(strict_types=1);

namespace Modules\Core\Rules;

use Illuminate\Validation\Rules\Password;

/**
 * Class PasswordRule
 * Provides various password validation rules.
 */
class PasswordRule
{
    /**
     * Get the default password rules.
     *
     * @param  bool  $confirmed  Whether the password should be confirmed.
     * @return array<int, string|Password> The array of validation rules.
     */
    public static function default(bool $confirmed = false): array
    {
        $rules = [
            'required',
            'string',
            Password::defaults(),
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Get the rules for changing a password.
     *
     * @return array<int, string|Password> The array of validation rules.
     */
    public static function changePassword(): array
    {
        return [
            'required',
            'string',
            Password::defaults(),
            'confirmed',
            'different:current_password',
        ];
    }

    /**
     * Get the rules for validating the current password.
     *
     * @return array<int, string> The array of validation rules.
     */
    public static function currentPassword(): array
    {
        return [
            'required',
            'string',
            'current_password',
        ];
    }

    /**
     * Get the rules for password confirmation.
     *
     * @return array<int, string> The array of validation rules.
     */
    public static function confirmation(): array
    {
        return [
            'required',
            'string',
        ];
    }

    /**
     * Get the optional password rules.
     *
     * @return array<int, string|Password> The array of validation rules.
     */
    public static function optional(): array
    {
        return [
            'nullable',
            'string',
            Password::defaults(),
        ];
    }
}
