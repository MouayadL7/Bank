<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;

class AuthenticatorAction
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function attempt(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Invalid credentials.',
            ]);
        }

        return $user;
    }
}
