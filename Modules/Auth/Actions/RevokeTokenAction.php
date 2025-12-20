<?php

namespace Modules\Auth\Actions;

use Modules\User\Models\User;

class RevokeToken
{
    public function revokeCurrent(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
