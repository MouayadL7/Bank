<?php

namespace Modules\Notifications\Observers;

use Modules\Notification\Interfaces\NotifiableEvent;
use Modules\Notification\Interfaces\NotificationObserver;
use Modules\Notification\Models\Notification;

class EmailNotificationObserver implements NotificationObserver
{
    public function notify(NotifiableEvent $event): void
    {
        Notification::create([
            'account_id' => $event->getData()['account_id'],
            'type'       => $event->getType(),
            'data'       => $event->getData(),
            'status'     => 'sent',
        ]);
    }
}

