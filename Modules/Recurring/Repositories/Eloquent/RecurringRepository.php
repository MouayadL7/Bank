<?php

namespace Modules\Recurring\Repositories\Eloquent;

use Modules\Recurring\Repositories\Interfaces\RecurringRepositoryInterface;
use Modules\Recurring\Models\Recurring;

class RecurringRepository implements RecurringRepositoryInterface
{
    protected $model;

    public function __construct(Recurring $model)
    {
        $this->model = $model;
    }
}
