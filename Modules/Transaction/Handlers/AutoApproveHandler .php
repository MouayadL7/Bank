<?php
namespace Modules\Transaction\Handlers;

use Illuminate\Support\Facades\Auth;
use Modules\Transaction\Enums\TransactionStatus;
use Modules\Transaction\Models\Transaction;

class AutoApproveHandler extends BaseTransactionHandler
{
    public function __construct()
    {
        $this->nextHandler = new ManagerApprovalHandler();
    }

    public function handle(Transaction $transaction): bool
    {
        if ($transaction->amount <= 1000) {
            $transaction->status = TransactionStatus::APPROVED->value;
            $transaction->approved_by = Auth::id();
            $transaction->approved_at = now();
            $transaction->save();

            return true;
        }

        return $this->nextHandler->handle($transaction);
    }
}
