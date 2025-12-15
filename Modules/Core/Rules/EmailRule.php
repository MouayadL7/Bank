<?php

declare(strict_types=1);

namespace Modules\Core\Rules;

use Illuminate\Validation\Rule;

class EmailRule
{
    public static function default(bool $unique = false, ?int $ignoreId = null): array
    {
        $rules = [
            'required',
            'string',
            Rule::email(),
            'max:255',
        ];

        if ($unique) {
            $rules[] = $ignoreId
                ? Rule::unique('users', 'email')->ignore($ignoreId)
                : Rule::unique('users', 'email');
        }

        return $rules;
    }

    public static function exists(): array
    {
        return [
            'required',
            'string',
            Rule::email(),
            'exists:users,email',
        ];
    }

    public static function optional(): array
    {
        return [
            'nullable',
            'string',
            Rule::email(),
            'max:255',
        ];
    }
}
