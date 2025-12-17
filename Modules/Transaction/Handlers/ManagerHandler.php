<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

class ManagerHandler extends AbstractApproval
{
    public function handle(Transaction $transaction): bool
    {
        // شرط الموافقة للمدير: 1000 < المبلغ <= 10000
        if ($transaction->amount > 1000 && $transaction->amount <= 10000) {
            $transaction->status = 'APPROVED';
            $transaction->approved_by = auth()->id();
            $transaction->save();
            return true;
        }

        return parent::handle($transaction);
    }
}
