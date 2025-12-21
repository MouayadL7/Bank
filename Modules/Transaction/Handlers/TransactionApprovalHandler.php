<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

interface TransactionApprovalHandler
{
    public function handle(Transaction $transaction): bool;
}
