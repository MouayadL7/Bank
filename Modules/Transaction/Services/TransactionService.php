<?php

namespace App\Modules\Transaction\Services;

use App\Modules\Transaction\Repositories\TransactionRepositoryInterface;
use Modules\Transaction\DTOs\TransactionDTO;
use Modules\Transaction\Http\Resources\TransactionResource;
use Modules\Transaction\Models\Transaction;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $repository,
    ) {}

    public function createTransaction(TransactionDTO $dto): TransactionResource
    {
        $transaction = $this->repository->create($dto->toArray());

        return new TransactionResource($transaction);
    }

    public function updateTransaction(Transaction $transaction, TransactionDTO $dto): TransactionResource
    {
        $transaction = $this->repository->update($transaction, $dto->toArray());

        return new TransactionResource($transaction);
    }

}

