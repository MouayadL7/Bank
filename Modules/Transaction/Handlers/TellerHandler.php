<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

class TellerHandler extends AbstractApproval
{
    public function handle(Transaction $transaction): bool
    {
        // شرط الموافقة للتيلر: المبلغ <= 1000
        if ($transaction->amount <= 1000) {
            $transaction->status = 'APPROVED';
            $transaction->approved_by = auth()->id(); // المستخدم الحالي
            $transaction->save();
            return true;
        }

        // تمرير المعاملة للمستوى التالي
        return parent::handle($transaction);
    }
}
