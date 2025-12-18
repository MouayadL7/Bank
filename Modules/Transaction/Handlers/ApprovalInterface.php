<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

interface ApprovalInterface
{
    public function setNext(ApprovalInterface $handler): ApprovalInterface;

    public function handle(Transaction $transaction): bool;
}
