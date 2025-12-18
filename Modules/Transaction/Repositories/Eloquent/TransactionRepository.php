<?php

namespace App\Modules\Transaction\Repositories;

use App\Modules\Transaction\Enums\TransactionStatus;
use Modules\Transaction\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);
        return $transaction;
    }

    public function getExecutableScheduledTransactions(): Collection
    {
        return Transaction::where('is_scheduled', true)
            ->where('scheduled_at', '<=', Carbon::now())
            ->where('status', TransactionStatus::PENDING)
            ->get();
    }

    public function markAsApproved(Transaction $transaction): void
    {
        $transaction->status = TransactionStatus::APPROVED;
        $transaction->save();
    }
}
