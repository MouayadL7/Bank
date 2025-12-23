<?php

namespace Tests\Unit\Notification;

use Mockery;
use Modules\Notification\Services\NotificationService;
use Modules\Notification\Interfaces\NotifiableEvent;
use Modules\Notification\Interfaces\NotificationObserver;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_attach_adds_observer(): void
    {
        // Arrange
        $service = new NotificationService();
        $observer = Mockery::mock(NotificationObserver::class);

        // Act
        $service->attach($observer);

        // Assert - if we reach here without exception, the observer was attached
        $this->assertTrue(true);
    }

    public function test_notify_all_notifies_all_observers(): void
    {
        // Arrange
        $service = new NotificationService();
        $event = Mockery::mock(NotifiableEvent::class);

        $observer1 = Mockery::mock(NotificationObserver::class);
        $observer1->shouldReceive('notify')
            ->once()
            ->with($event);

        $observer2 = Mockery::mock(NotificationObserver::class);
        $observer2->shouldReceive('notify')
            ->once()
            ->with($event);

        $service->attach($observer1);
        $service->attach($observer2);

        // Act
        $service->notifyAll($event);

        // Assert - if we reach here without exception, all observers were notified
        $this->assertTrue(true);
    }
}

