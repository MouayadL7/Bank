<?php

namespace Modules\AccessControl\Repositories\Eloquent;

use Modules\AccessControl\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\AccessControl\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }
}
