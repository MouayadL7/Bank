<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

abstract class AbstractApproval implements ApprovalInterface
{
    protected ?ApprovalInterface $nextHandler = null;

    /**
     * تحديد المعالج التالي في السلسلة
     */
    public function setNext(ApprovalInterface $handler): ApprovalInterface
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * معالجة المعاملة أو تمريرها للمستوى التالي
     */
    public function handle(Transaction $transaction): bool
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($transaction);
        }
        return false; // لم يتم الموافقة
    }
}
