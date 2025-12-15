<?php

namespace Modules\Shared\Repositories\Eloquent;

use Modules\Shared\Repositories\Interfaces\SharedRepositoryInterface;
use Modules\Shared\Models\Shared;

class SharedRepository implements SharedRepositoryInterface
{
    protected $model;

    public function __construct(Shared $model)
    {
        $this->model = $model;
    }
}
