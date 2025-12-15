<?php

declare(strict_types=1);

namespace Modules\Core\Rules;

use Illuminate\Validation\Rule;

class StringRule
{
    public static function default(bool $required = true, int $min = 2, int $max = 255): array
    {
        $rules = [
            'string',
            "min:{$min}",
            "max:{$max}",
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    public static function name(): array
    {
        return [
            'required',
            'string',
            'min:2',
            'max:255',
            'regex:/^[\pL\s\-]+$/u', // Letters, spaces, and hyphens only
        ];
    }

    public static function username(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:30',
            'regex:/^[a-zA-Z0-9_-]+$/', // Alphanumeric, underscore, and hyphen only
            'unique:users,username',
        ];
    }

    public static function phone(): array
    {
        return [
            'nullable',
            'string',
            'regex:/^([0-9\s\-\+\(\)]*)$/',
            'min:10',
            'max:20',
        ];
    }

    public static function slug(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:255',
            'regex:/^[a-z0-9-]+$/', // Lowercase letters, numbers, and hyphens only
        ];
    }

    public static function title(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:255',
            'regex:/^[\pL\s\d\-\_\.\,\!\?]+$/u', // Letters, numbers, spaces, and basic punctuation
        ];
    }

    public static function description(int $maxLength = 1000): array
    {
        return [
            'nullable',
            'string',
            'max:'.$maxLength,
        ];
    }

    public static function enum(string $enumClass): array
    {
        return [
            'required',
            'string',
            Rule::enum($enumClass),
        ];
    }

    public static function search(): array
    {
        return [
            'nullable',
            'string',
            'min:2',
            'max:100',
        ];
    }

    public static function password(bool $required = true): array
    {
        return PasswordRule::default($required);
    }

    public static function email(bool $unique = false, ?int $ignoreId = null): array
    {
        return EmailRule::default($unique, $ignoreId);
    }
}
