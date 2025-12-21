<?php

namespace Modules\Auth\Actions;

use Modules\User\Models\User;

class RevokeTokenAction
{
    public function revokeCurrent(User $user): void
    {
        $user->tokens()->delete();
    }
}
