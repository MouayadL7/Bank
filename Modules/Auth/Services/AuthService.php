<?php

namespace Modules\Auth\Services;

use Modules\Auth\Actions\Authenticator;
use Modules\Auth\Actions\RevokeToken;
use Modules\Auth\Actions\UserStatusChecker;
use Modules\Auth\Actions\TokenIssuer;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class AuthService
{
    public function __construct(
        private Authenticator $authenticator,
        private UserStatusChecker $statusChecker,
        private TokenIssuer $tokenIssuer,
        private RevokeToken $revokeToken
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
