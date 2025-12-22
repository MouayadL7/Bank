<?php

namespace Modules\User\Services;

use Modules\User\DTOs\UserData;
use Modules\User\Enums\UserStatus;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function create(UserData $dto): User
    {
        $existsingUser = $this->repo->findByEmail($dto->email);

        if ($existsingUser) {
            return $existsingUser;
        }

        return $this->repo->create($dto->toArray());
    }

    public function list(array $filters)
    {
        $users = $this->repo->list($filters);

        return UserResource::collection($users)->resource;
    }

    public function search(array $filters)
    {
        $users = $this->repo->list($filters);

        return UserResource::collection($users)->resource;
    }

    public function getByUuid(string $uuid): UserResource
    {
        $user = $this->repo->findByUuid($uuid, true);

        return new UserResource($user);
    }

    public function suspend(string $uuid): UserResource
    {
        $user = $this->repo->findByUuid($uuid);
        $user->status = UserStatus::SUSPENDED;
        $user = $this->repo->save($user);

        return new UserResource($user);
    }

    public function activate(string $uuid): UserResource
    {
        $user = $this->repo->findByUuid($uuid);
        $user->status = UserStatus::ACTIVE;
        $user = $this->repo->save($user);

        return new UserResource($user);
    }
}
