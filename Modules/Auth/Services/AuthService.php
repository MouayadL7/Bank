<?php

namespace Modules\Auth\Services;

use Modules\Auth\Actions\AuthenticatorAction;
use Modules\Auth\Actions\RevokeTokenAction;
use Modules\Auth\Actions\UserStatusCheckerAction;
use Modules\Auth\Actions\TokenIssuerAction;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class AuthService
{
    public function __construct(
        private AuthenticatorAction $authenticator,
        private UserStatusCheckerAction $statusChecker,
        private TokenIssuerAction $tokenIssuer,
        private RevokeTokenAction $revokeToken
    ) {}

    public function login(string $email, string $password)
    {
        $user = $this->authenticator->attempt($email, $password);

        $this->statusChecker->ensureActive($user);

        $token = $this->tokenIssuer->issue($user);

        return [
            'user'  => new UserResource($user),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $this->revokeToken->revokeCurrent($user);
    }
}
