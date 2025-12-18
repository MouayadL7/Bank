<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

abstract class AbstractApproval implements ApprovalInterface
{
    protected ?ApprovalInterface $nextHandler = null;

    public function setNext(ApprovalInterface $handler): ApprovalInterface
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(Transaction $transaction): bool
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($transaction);
        }
        return false;
    }
}
