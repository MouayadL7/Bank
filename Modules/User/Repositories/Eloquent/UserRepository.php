<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function list(array $filters)
    {
        return User::with('role')
            ->filter($filters)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findByUuid(string $uuid, bool $load = false): User
    {
        return User::where('uuid', $uuid)
            ->when($load, fn($q) => $q->with('accounts'))
            ->firstOrFail();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user): User
    {
        $user->save();
        return $user;
    }
}
