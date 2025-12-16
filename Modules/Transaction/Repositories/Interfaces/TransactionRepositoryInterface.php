<?php

namespace App\Modules\Transaction\Repositories;

use Modules\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;
    public function update(Transaction $transaction, array $data): Transaction;

}
