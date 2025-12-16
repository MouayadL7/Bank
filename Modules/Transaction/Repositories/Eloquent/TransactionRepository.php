<?php

namespace App\Modules\Transaction\Repositories;

use Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Repositories\TransactionRepositoryInterface;

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
}
