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
        $this->service = $service;
    }

    // إنشاء معاملة
    public function createTransaction(TransactionDTO $dto)
    {
        return $this->service->create($dto);
    }

    // تحديث معاملة
    public function updateTransaction(Transaction $transaction, TransactionDTO $dto)
    {
        return $this->service->update($transaction, $dto);
    }

    // تنفيذ المعاملات المجدولة
    public function processScheduled()
    {
        return $this->service->processScheduledTransactions();
    }

    // الموافقة على المعاملة
    public function approve(Transaction $transaction)
    {
        return $this->service->approveTransaction($transaction);
    }
}
