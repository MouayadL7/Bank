<?php

namespace Modules\Auth\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Modules\User\Models\User;

class UserStatusChecker
{
    public function ensureActive(User $user): void
    {
        if (!$user->isActive()) {
            throw new AuthorizationException('User is not active');
        }
    }
}
