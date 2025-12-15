<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
