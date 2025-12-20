<?php

namespace Modules\AccessControl\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\AccessControl\DTOs\RoleData;
use Modules\AccessControl\Http\Resources\RoleResource;
use Modules\AccessControl\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService
{
    public function __construct(private RoleRepositoryInterface $repo) {}

    public function getAll(): AnonymousResourceCollection
    {
        $roles = $this->repo->all();

        return RoleResource::collection($roles);
    }

    public function createRole(RoleData $dto): RoleResource
    {
        $role = $this->repo->create($dto->toArray());

        return new RoleResource($role);
    }
}
