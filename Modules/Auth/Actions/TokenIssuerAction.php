<?php

namespace Modules\Auth\Actions;

use Modules\User\Models\User;

class TokenIssuer
{
    public function issue(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }
}
