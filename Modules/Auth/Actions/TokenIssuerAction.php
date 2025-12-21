<?php

namespace Modules\Auth\Actions;

use Modules\User\Models\User;

class TokenIssuerAction
{
    public function issue(User $user): string
    {
        $user->tokens()->delete();
        return $user->createToken('api')->plainTextToken;
    }
}
