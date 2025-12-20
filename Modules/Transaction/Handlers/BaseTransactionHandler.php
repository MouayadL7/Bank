<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

abstract class BaseTransactionHandler implements TransactionApprovalHandler
{
    protected ?TransactionApprovalHandler $nextHandler = null;

    public function handle(Transaction $transaction): bool
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($transaction);
        }
        
        return false;
    }
}
