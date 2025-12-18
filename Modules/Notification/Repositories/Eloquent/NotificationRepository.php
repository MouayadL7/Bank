<?php

namespace Modules\Notification\Repositories\Eloquent;

use Modules\Notification\Repositories\Interfaces\NotificationRepositoryInterface;
use Modules\Notification\Models\Notification;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }
}
