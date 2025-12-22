<?php

namespace Modules\Transaction\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function update(Transaction $transaction, array $data): Transaction;

    public function getExecutableScheduledTransactions(): Collection;

    public function markAsApproved(Transaction $transaction): void;

    public function findByUuid(string $uuid): Transaction;
    public function getPending(): Collection;
}
