<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function list(array $filters)
    {
        return User::with('role')
            ->filter($filters)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findByUuid(string $uuid): User
    {
        return User::where('uuid', $uuid)->firstOrFail();
    }

    public function findByEmail(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function save(User $user): User
    {
        $user->save();
        return $user;
    }
}
