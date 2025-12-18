<?php

namespace Modules\Customer\Notifications\Services;

use Modules\Notification\Events\BalanceChangedEvent;

class TransactionService
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function deposit(int $accountId, float $amount, float $oldBalance): void
    {
        $newBalance = $oldBalance + $amount;
        // ... تنفيذ منطق الإيداع

        $event = new BalanceChangedEvent($accountId, $oldBalance, $newBalance);
        $this->notificationService->notifyAll($event);
    }

    public function withdraw(int $accountId, float $amount, float $oldBalance): void
    {
        $newBalance = $oldBalance - $amount;
        // ... تنفيذ منطق السحب

        $event = new BalanceChangedEvent($accountId, $oldBalance, $newBalance);
        $this->notificationService->notifyAll($event);
    }

    public function transfer(int $fromAccountId, int $toAccountId, float $amount, float $fromOldBalance, float $toOldBalance): void
    {
        $fromNewBalance = $fromOldBalance - $amount;
        $toNewBalance   = $toOldBalance + $amount;
        // ... تنفيذ منطق التحويل

        $fromEvent = new BalanceChangedEvent($fromAccountId, $fromOldBalance, $fromNewBalance);
        $toEvent   = new BalanceChangedEvent($toAccountId, $toOldBalance, $toNewBalance);

        $this->notificationService->notifyAll($fromEvent);
        $this->notificationService->notifyAll($toEvent);
    }
}
