<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Enums\TransactionStatus;
use Modules\Transaction\Events\ManagerApprovalRequired;
use Modules\Transaction\Models\Transaction;

class ManagerApprovalHandler extends BaseTransactionHandler
{
    public function handle(Transaction $transaction): bool
    {
        $transaction->status = TransactionStatus::PENDING->value;
        $transaction->save();

        event(new ManagerApprovalRequired($transaction));

        return true;
    }
}
