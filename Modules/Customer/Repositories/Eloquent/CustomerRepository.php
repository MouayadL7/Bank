<?php

namespace Modules\Customer\Repositories\Eloquent;

use Modules\Customer\Repositories\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }
}
