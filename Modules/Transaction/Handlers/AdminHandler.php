<?php
namespace Modules\Transaction\Handlers;

use Modules\Transaction\Models\Transaction;

class AdminHandler extends AbstractApproval
{
    public function handle(Transaction $transaction): bool
    {
        $transaction->status = 'APPROVED';
        $transaction->approved_by = auth()->id();
        $transaction->save();
        return true;
    }
}
