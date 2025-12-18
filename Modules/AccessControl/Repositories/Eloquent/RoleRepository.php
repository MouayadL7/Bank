<?php

namespace Modules\AccessControl\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Modules\AccessControl\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\AccessControl\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function all(): Collection
    {
        return Role::all();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }
}
