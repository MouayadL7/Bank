<?php

namespace App\Modules\Transaction\Services;

use App\Modules\Transaction\Repositories\TransactionRepositoryInterface;
use Modules\Transaction\DTOs\TransactionDTO;
use Modules\Transaction\Http\Resources\TransactionResource;
use Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Enums\TransactionType;
use Modules\Account\Events\AccountBalanceUpdated;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $repository
    ) {}

    public function create(TransactionDTO $dto): TransactionResource
    {
        $transaction = $this->repository->create($dto->toArray());

        if (!$transaction->is_scheduled) {
            $this->dispatchTransactionEvent($transaction);
        }

        return new TransactionResource($transaction);
    }

    public function update(Transaction $transaction, TransactionDTO $dto): TransactionResource
    {
        $transaction = $this->repository->update($transaction, $dto->toArray());
        return new TransactionResource($transaction);
    }

    public function processScheduledTransactions(): void
    {
        $transactions = $this->repository->getExecutableScheduledTransactions();

        foreach ($transactions as $transaction) {
            $this->dispatchTransactionEvent($transaction);
            $this->repository->markAsApproved($transaction);
        }
    }

    private function dispatchTransactionEvent(Transaction $transaction): void
    {
        match ($transaction->type) {
            TransactionType::DEPOSIT =>
                event(new AccountBalanceUpdated(
                    $transaction->toAccount,
                    $transaction->amount,
                    'deposit'
                )),

            TransactionType::WITHDRAWAL =>
                event(new AccountBalanceUpdated(
                    $transaction->fromAccount,
                    -$transaction->amount,
                    'withdraw'
                )),

            TransactionType::TRANSFER =>
                event(new AccountBalanceUpdated(
                    $transaction->fromAccount,
                    -$transaction->amount,
                    'transfer',
                    $transaction->fromAccount->id,
                    $transaction->toAccount->id
                )),
        };
    }
}
