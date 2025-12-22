<?php

namespace Modules\Transaction\Repositories\Eloquent;

use Modules\Transaction\Enums\TransactionStatusEnum;
use Modules\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Modules\Transaction\Repositories\Interfaces\TransactionRepositoryInterface;
use Carbon\Carbon;

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
            ->where('status', TransactionStatusEnum::PENDING)
            ->get();
    }

    public function findByUuid(string $uuid): Transaction
    {
        return Transaction::where('uuid', $uuid)->firstOrFail();
    }

    public function getPending(): Collection
    {
        return Transaction::Where('status', TransactionStatusEnum::PENDING->value)->get();
    }
}
