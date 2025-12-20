<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Modules\User\Models\User;

class Authenticator
{
    public function attempt(string $email, string $password): ?User
    {
        if (!Auth::attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            throw new AuthenticationException();
        }

        return Auth::user();
    }
}
