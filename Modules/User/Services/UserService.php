<?php

namespace Modules\User\Services;

use Modules\User\Enums\UserStatus;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function list(array $filters)
    {
        $users = $this->repo->list($filters);

        return UserResource::collection($users);
    }

    public function search(array $filters)
    {
        $users = $this->repo->list($filters);

        return UserResource::collection($users);
    }

    public function getByUuid(string $uuid): User
    {
        return $this->repo->findByUuid($uuid);
    }

    public function suspend(string $uuid): UserResource
    {
        $user = $this->getByUuid($uuid);
        $user->status = UserStatus::SUSPENDED;
        $user = $this->repo->save($user);

        return new UserResource($user);
    }

    public function activate(string $uuid): UserResource
    {
        $user = $this->getByUuid($uuid);
        $user->status = UserStatus::ACTIVE;
        $user = $this->repo->save($user);

        return new UserResource($user);
    }
}
