<?php

declare(strict_types=1);

namespace Modules\Core\Rules;

use Illuminate\Validation\Rule;

class RelationRule
{
    public static function exists(string $table, string $column = 'id', bool $required = false): array
    {
        $rules = [
            'integer',
            Rule::exists($table, $column),
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    public static function belongsTo(string $table, string $column = 'id', bool $required = true): array
    {
        return self::exists($table, $column, $required);
    }

    public static function unique(string $table, string $column, ?int $ignoreId = null): array
    {
        $rules = [
            'required',
            Rule::unique($table, $column),
        ];

        if ($ignoreId !== null) {
            $rules[1] = Rule::unique($table, $column)->ignore($ignoreId);
        }

        return $rules;
    }

    public static function multipleExists(string $table, string $column = 'id', bool $required = false): array
    {
        $rules = [
            'array',
            'min:1',
            Rule::exists($table, $column),
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    public static function belongsToMany(string $table, string $column = 'id', bool $required = false): array
    {
        return self::multipleExists($table, $column, $required);
    }
}
