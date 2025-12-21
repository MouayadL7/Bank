<?php
namespace Modules\Transaction\Facades;

use App\Modules\Transaction\Services\TransactionService;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\DTOs\TransactionDTO;

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
        $this->service->transfare($fromUuid, $toUuid, $amount);
    }

    public function processScheduled()
    {
        return $this->service->processScheduledTransactions();
    }

    public function approve(Transaction $transaction)
    {
        return $this->service->approveTransaction($transaction);
    }
}
