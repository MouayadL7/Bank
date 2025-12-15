<?php

namespace Modules\AccessControl\Repositories\Eloquent;

use Modules\AccessControl\Repositories\Interfaces\AccessControlRepositoryInterface;
use Modules\AccessControl\Models\AccessControl;

class AccessControlRepository implements AccessControlRepositoryInterface
{
    protected $model;

    public function __construct(AccessControl $model)
    {
        $this->model = $model;
    }
}
