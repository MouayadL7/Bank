<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Enums\TransactionStatusEnum;
use Modules\Transaction\Events\ManagerApprovalRequired;
use Modules\Transaction\Models\Transaction;

class ManagerApprovalHandler extends BaseTransactionHandler
{
    public function handle(Transaction $transaction): bool
    {
        $transaction->status = TransactionStatusEnum::PENDING->value;
        $transaction->save();

        event(new ManagerApprovalRequired($transaction));

        return true;
    }
}
