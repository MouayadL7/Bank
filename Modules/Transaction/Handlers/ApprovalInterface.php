<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

interface ApprovalInterface
{
    /**
     * تحديد المعالج التالي في السلسلة
     */
    public function setNext(ApprovalInterface $handler): ApprovalInterface;

    /**
     * معالجة المعاملة
     */
    public function handle(Transaction $transaction): bool;
}
