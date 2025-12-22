<?php

namespace Modules\Notification\Services;

use Modules\Notification\Interfaces\NotifiableEvent;
use Modules\Notification\Interfaces\NotificationObserver;

class NotificationService
{
    /**
     * @var NotificationObserver[]
     */
    private array $observers = [];

    public function attach(NotificationObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notifyAll(NotifiableEvent $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->notify($event);
        }
    }
}
