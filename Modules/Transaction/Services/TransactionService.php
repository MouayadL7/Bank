<?php

namespace Modules\Transaction\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Transaction\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Transaction\Actions\DepositAction;
use Modules\Transaction\Actions\TransferAction;
use Modules\Transaction\Actions\WithdrawAction;
use Modules\Transaction\Events\AccountBalanceUpdated;
use Modules\Transaction\Handlers\AutoApprovalHandler;
use Modules\Transaction\Http\Resources\TransactionResource;
use Modules\Transaction\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Transaction\Enums\TransactionTypeEnum;

class TransactionService
{
    use AuthorizesRequests;

    public function __construct(
        private TransactionRepositoryInterface $repository,
        private AccountRepositoryInterface $accountRepository,
        private DepositAction $depositAction,
        private WithdrawAction $withdrawAction,
        private TransferAction $transferAction,
        private AutoApprovalHandler $autoApproveHandler
    ) {}

    public function getPending()
    {
        $transactions = $this->repository->getPending();

        return TransactionResource::collection($transactions);
    }

    public function getAccountTransactions(string $uuid)
    {
        $account = $this->accountRepository->findByUuid($uuid);
        $transactions = $this->repository->getByAccountId($account->id);

        return TransactionResource::collection($transactions);
    }

    private function createTransaction(int $from, int $to, $amount, string $type, bool $isScheduled = false, ?Carbon $scheduledAt = null)
    {
        return $transaction = $this->repository->create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'from_account_id' => $from,
            'to_account_id' => $to,
            'amount' => $amount,
            'type' => $type,
            'is_scheduled' => $isScheduled,
            'scheduled_at' => $scheduledAt,
            'created_by' => auth()->id(),
        ]);

        return new TransactionResource($transaction);
    }

    public function deposit(string $uuid, float $amount, ?int $byUserId = null): TransactionResource
    {
        return DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->accountRepository->findByUuid($uuid);

            $this->authorize('deposit', $account);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $account->id,
                to: $account->id,
                amount: $amount,
                type: TransactionTypeEnum::DEPOSIT->value
            );

            // Transaction Handler
            $this->autoApproveHandler->handle($transaction);

            if ($transaction->isApproved()) {
                // apply domain rules
                $updatedAccount = $this->depositAction->execute($account, $amount);

                // persist changes
                $this->accountRepository->save($updatedAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    fromAccount: $account,
                    toAccount: $account,
                    amount: $amount,
                    transactionType: TransactionTypeEnum::DEPOSIT->value
                ));
            }

            return new TransactionResource($transaction);
        });
    }

    public function withdraw(string $uuid, float $amount, ?int $byUserId = null)
    {
        return DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->accountRepository->findByUuid($uuid);

            $this->authorize('withdraw', $account);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $account->id,
                to: $account->id,
                amount: $amount,
                type: TransactionTypeEnum::WITHDRAWAL->value,
            );

            // Transaction Handler
            $this->autoApproveHandler->handle($transaction);

            if ($transaction->isApproved()) {
                // apply domain rules
                $updatedAccount = $this->withdrawAction->execute($account, $amount);

                // persist changes
                $this->accountRepository->save($updatedAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    fromAccount: $account,
                    toAccount: $account,
                    amount: $amount,
                    transactionType: TransactionTypeEnum::WITHDRAWAL->value
                ));
            }

            return new TransactionResource($transaction);
        });
    }

    public function transfare(string $fromUUID, string $toUUID, float $amount)
    {
        return DB::transaction(function () use ($fromUUID, $toUUID, $amount) {
            $fromAccount = $this->accountRepository->findByUuid($fromUUID);
            $toAccount = $this->accountRepository->findByUuid($toUUID);

            $this->authorize('transfer', $fromAccount);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $fromAccount->id,
                to: $toAccount->id,
                amount: $amount,
                type: TransactionTypeEnum::TRANSFER->value,
            );

            // Transaction Handler
            $this->autoApproveHandler->handle($transaction);

            if ($transaction->isApproved()) {
                // apply domain rules
                $this->transferAction->execute($fromAccount, $toAccount, $amount);

                // persist changes
                $this->accountRepository->save($fromAccount);
                $this->accountRepository->save($toAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    $fromAccount,
                    $toAccount,
                    $amount,
                    TransactionTypeEnum::TRANSFER->value
                ));
            }

            return new TransactionResource($transaction);
        });
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
            TransactionTypeEnum::DEPOSIT =>
                event(new AccountBalanceUpdated(
                    $transaction->toAccount,
                    $transaction->amount,
                    'deposit'
                )),

            TransactionTypeEnum::WITHDRAWAL =>
                event(new AccountBalanceUpdated(
                    $transaction->fromAccount,
                    -$transaction->amount,
                    'withdraw'
                )),

            TransactionTypeEnum::TRANSFER =>
                event(new AccountBalanceUpdated(
                    $transaction->fromAccount,
                    -$transaction->amount,
                    'transfer',
                    $transaction->fromAccount->id,
                    $transaction->toAccount->id
                )),
        };
    }

    public function approveTransaction(string $uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $transaction = $this->repository->findByUuid($uuid);

            // Check if transaction already been Approved
            if ($transaction->isApproved()) {
                throw new \Exception('Transaction has already been approved.');
            }

            $fromAccount = $transaction->fromAccount;
            $toAccount = $transaction->toAccount;
            $amount = $transaction->amount;

            // Approve Transaction
            $transaction->approve(Auth::id());

            if ($transaction->type === TransactionTypeEnum::DEPOSIT) {
                // apply domain rules
                $updatedAccount = $this->depositAction->execute($fromAccount, $amount);

                // persist changes
                $this->accountRepository->save($updatedAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    fromAccount: $fromAccount,
                    toAccount: $toAccount,
                    amount: $amount,
                    transactionType: TransactionTypeEnum::DEPOSIT->value
                ));
            }
            else if ($transaction->type === TransactionTypeEnum::WITHDRAWAL) {
                // apply domain rules
                $updatedAccount = $this->withdrawAction->execute($fromAccount, $amount);

                // persist changes
                $this->accountRepository->save($updatedAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    fromAccount: $fromAccount,
                    toAccount: $toAccount,
                    amount: $amount,
                    transactionType: TransactionTypeEnum::WITHDRAWAL->value
                ));
            }
            else {
                // apply domain rules
                $this->transferAction->execute($fromAccount, $toAccount, $amount);

                // persist changes
                $this->accountRepository->save($fromAccount);
                $this->accountRepository->save($toAccount);

                // events are not responsibility of this method
                event(new AccountBalanceUpdated(
                    $fromAccount,
                    $toAccount,
                    $amount,
                    TransactionTypeEnum::TRANSFER->value
                ));
            }

            return new TransactionResource($transaction);
        });
    }

    public function rejectTransaction(string $uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $transaction = $this->repository->findByUuid($uuid);

            // Check if transaction already been Rejected
            if ($transaction->isRejected()) {
                throw new \Exception('Transaction has already been rejected.');
            }

            // Reject Transaction
            $transaction->reject(Auth::id());

            return new TransactionResource($transaction);
        });
    }
}
