<?php

namespace Modules\Notification\Services;

use Modules\Notification\Events\LargeTransactionEvent;
use Modules\Notification\Events\BalanceChangedEvent;

class TransactionService
{
    private NotificationService $notificationService;
    private float $largeTransactionThreshold = 10000; // مثال: 10,000 $

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function deposit(int $accountId, float $amount, float $oldBalance): void
    {
        $newBalance = $oldBalance + $amount;
        // ... منطق الإيداع

        $event = new BalanceChangedEvent($accountId, $oldBalance, $newBalance);
        $this->notificationService->notifyAll($event);

        // تحقق من Large Transaction
        if ($amount >= $this->largeTransactionThreshold) {
            $largeEvent = new LargeTransactionEvent($accountId, $amount, 'deposit');
            $this->notificationService->notifyAll($largeEvent);
        }
    }

    public function withdraw(int $accountId, float $amount, float $oldBalance): void
    {
        $newBalance = $oldBalance - $amount;
        // ... منطق السحب

        $event = new BalanceChangedEvent($accountId, $oldBalance, $newBalance);
        $this->notificationService->notifyAll($event);

        if ($amount >= $this->largeTransactionThreshold) {
            $largeEvent = new LargeTransactionEvent($accountId, $amount, 'withdraw');
            $this->notificationService->notifyAll($largeEvent);
        }
    }

    public function transfer(int $fromAccountId, int $toAccountId, float $amount, float $fromOldBalance, float $toOldBalance): void
    {
        $fromNewBalance = $fromOldBalance - $amount;
        $toNewBalance   = $toOldBalance + $amount;
        // ... منطق التحويل

        $fromEvent = new BalanceChangedEvent($fromAccountId, $fromOldBalance, $fromNewBalance);
        $toEvent   = new BalanceChangedEvent($toAccountId, $toOldBalance, $toNewBalance);

        $this->notificationService->notifyAll($fromEvent);
        $this->notificationService->notifyAll($toEvent);

        if ($amount >= $this->largeTransactionThreshold) {
            $largeEventFrom = new LargeTransactionEvent($fromAccountId, $amount, 'transfer_out');
            $largeEventTo   = new LargeTransactionEvent($toAccountId, $amount, 'transfer_in');
            $this->notificationService->notifyAll($largeEventFrom);
            $this->notificationService->notifyAll($largeEventTo);
        }
    }
}


