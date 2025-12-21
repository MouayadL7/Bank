<?php

namespace Modules\Transaction\Repositories\Interfaces;

use Modules\Transaction\Models\Transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function update(Transaction $transaction, array $data): Transaction;

    public function getExecutableScheduledTransactions(): Collection;

    public function markAsApproved(Transaction $transaction): void;
}
