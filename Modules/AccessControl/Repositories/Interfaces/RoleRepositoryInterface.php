<?php

namespace Modules\AccessControl\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\AccessControl\Models\Role;

interface RoleRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): Role;
}
