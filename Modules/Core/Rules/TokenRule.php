<?php

declare(strict_types=1);

namespace Modules\Core\Rules;

class TokenRule
{
    public static function default(int $length = 60): array
    {
        return [
            'required',
            'string',
            "size:{$length}",
        ];
    }

    public static function passwordReset(): array
    {
        return self::default(60); // Laravel's default password reset token length
    }

    public static function sanctum(): array
    {
        return [
            'required',
            'string',
            'size:80', // Typical Laravel Sanctum token length
        ];
    }

    public static function verification(): array
    {
        return [
            'required',
            'string',
            'size:40', // Common verification token length
        ];
    }

    public static function jwt(): array
    {
        return [
            'required',
            'string',
            'regex:/^[A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.[A-Za-z0-9-_.+/=]*$/', // JWT format validation
        ];
    }

    public static function oauth(): array
    {
        return [
            'required',
            'string',
            'regex:/^[a-zA-Z0-9]{64}$/', // 64 characters, alphanumeric
        ];
    }

    public static function otp(int $length = 6): array
    {
        return [
            'required',
            'string',
            "digits:{$length}",
        ];
    }

    public static function custom(int $minLength, int $maxLength, ?string $pattern = null): array
    {
        $rules = [
            'required',
            'string',
            'min:'.$minLength,
            'max:'.$maxLength,
        ];

        if ($pattern) {
            $rules[] = 'regex:'.$pattern;
        }

        return $rules;
    }

    public static function apiKey(): array
    {
        return [
            'required',
            'string',
            'size:32',
            'regex:/^[A-Za-z0-9]+$/', // Alphanumeric only
        ];
    }

    public static function hash(string $algorithm = 'sha256'): array
    {
        $lengths = [
            'md5' => 32,
            'sha1' => 40,
            'sha256' => 64,
            'sha512' => 128,
        ];

        return [
            'required',
            'string',
            'size:'.($lengths[$algorithm] ?? 64),
            'regex:/^[A-Fa-f0-9]+$/', // Hexadecimal only
        ];
    }
}
