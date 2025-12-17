<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

class AdminHandler extends AbstractApproval
{
    public function handle(Transaction $transaction): bool
    {
        // أي مبلغ أكبر من 10000 يوافق عليه المدير الأعلى (Admin)
        $transaction->status = 'APPROVED';
        $transaction->approved_by = auth()->id();
        $transaction->save();
        return true;
    }
}
