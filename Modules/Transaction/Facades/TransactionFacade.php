<?php
namespace Modules\Transaction\Facades;

use Modules\Transaction\Services\TransactionService;
use Modules\Transaction\Models\Transaction;

class TransactionFacade
{
    protected TransactionService $service;

    public function __construct(TransactionService $service)
    {
        return $this->service = $service;
    }

    public function deposit(string $uuid, float $amount)
    {
        return $this->service->deposit($uuid, $amount);
    }

    public function withdraw(string $uuid, float $amount)
    {
        return $this->service->withdraw($uuid, $amount);
    }

    public function transfer(string $fromUuid, string $toUuid, float $amount)
    {
        return $this->service->transfare($fromUuid, $toUuid, $amount);
    }

    public function processScheduled()
    {
        return $this->service->processScheduledTransactions();
    }

    public function approve(string $uuid)
    {
        return $this->service->approveTransaction($uuid);
    }

    public function reject(string $uuid)
    {
        return $this->service->rejectTransaction($uuid);
    }

    public function getPending()
    {
        return $this->service->getPending();
    }

    public function getAccountTransactions(string $uuid)
    {
        return $this->service->getAccountTransactions($uuid);
    }
}
