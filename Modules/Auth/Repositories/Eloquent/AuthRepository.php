<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\Auth\Repositories\Interfaces\AuthRepositoryInterface;
use Modules\Auth\Models\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    protected $model;

    public function __construct(Auth $model)
    {
        $this->model = $model;
    }
}
