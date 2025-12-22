<?php

use Illuminate\Support\Str;

if (! function_exists('generateStrongPassword')) {
    /**
     * A generic helper function for Password Generation.
     *
     * @param int $length
     * @return string
     */
    function generateStrongPassword(int $length = 12): string
    {
        $upperCase = Str::upper(Str::random(2));
        $lowerCase = Str::lower(Str::random(2));
        $numbers = rand(10, 99);
        $symbols = '!@#$%^&*';
        $symbol = $symbols[rand(0, strlen($symbols) - 1)];

        $password = $upperCase . $lowerCase . $numbers . $symbol;

        $remainingLength = $length - strlen($password);
        if ($remainingLength > 0) {
            $password .= Str::random($remainingLength);
        }

        return str_shuffle($password);
    }
}
