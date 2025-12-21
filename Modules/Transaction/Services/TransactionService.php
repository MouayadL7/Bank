<?php

namespace App\Modules\Transaction\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Transaction\Actions\DepositAction;
use Modules\Transaction\Actions\TransferAction;
use Modules\Transaction\Actions\WithdrawAction;
use Modules\Transaction\Events\AccountBalanceUpdated;
use Modules\Transaction\Handlers\AutoApproveHandler;
use Modules\Transaction\Http\Resources\TransactionResource;
use Modules\Transaction\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionService
{
    use AuthorizesRequests;

    public function __construct(
        private TransactionRepositoryInterface $repository,
        private AccountRepositoryInterface $accountRepository,
        private DepositAction $depositAction,
        private WithdrawAction $withdrawAction,
        private TransferAction $transferAction,
        private AutoApproveHandler $autoApproveHandler
    ) {}

    public function createTransaction(int $from, int $to, $amount, string $type, bool $isScheduled = false, ?Carbon $scheduledAt = null)
    {
        return $transaction = $this->repository->create([
            'from_account_id' => $from,
            'to_account_id' => $to,
            'amount' => $amount,
            'type' => $type,
            'is_scheduled' => $isScheduled,
            'scheduled_at' => $scheduledAt,
        ]);

        return new TransactionResource($transaction);
    }

    public function deposit(string $uuid, float $amount, ?int $byUserId = null): TransactionResource
    {
        $this->authorize('deposit', $uuid);

        return DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->accountRepository->findByUuid($uuid);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $account->id,
                to: $account->id,
                amount: $amount,
                type: TransactionType::DEPOSIT->value
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
                    transactionType: TransactionType::DEPOSIT->value
                ));
            }

            return new TransactionResource($transaction);
        });
    }

    public function withdraw(string $uuid, float $amount, ?int $byUserId = null)
    {
        $this->authorize('withdraw', $uuid);

        DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->accountRepository->findByUuid($uuid);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $account->id,
                to: $account->id,
                amount: $amount,
                type: TransactionType::WITHDRAWAL->value,
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
                    transactionType: TransactionType::WITHDRAWAL->value
                ));
            }
        });
    }

    public function transfare(string $fromUUID, string $toUUID, float $amount)
    {
        $this->authorize('transfer', $fromUUID);

        DB::transaction(function () use ($fromUUID, $toUUID, $amount) {
            $fromAccount = $this->accountRepository->findByUuid($fromUUID);
            $toAccount = $this->accountRepository->findByUuid($toUUID);

            // Store new transaction
            $transaction = $this->createTransaction(
                from: $fromAccount->id,
                to: $toAccount->id,
                amount: $amount,
                type: TransactionType::TRANSFER->value,
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
                    TransactionType::TRANSFER->value
                ));
            }
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

    public function approveTransaction(Transaction $transaction)
    {
        // Check if transaction already been Approved
        if ($transaction->isApproved()) {
            throw new \Exception('Transaction has already been approved.');
        }

        $fromAccount = $transaction->fromAccount;
        $toAccount = $transaction->toAccount;
        $amount = $transaction->amount;

        // Approve Transaction
        $transaction->approve(Auth::id());

        if ($transaction->type === TransactionType::DEPOSIT) {
            // apply domain rules
            $updatedAccount = $this->depositAction->execute($fromAccount, $amount);

            // persist changes
            $this->accountRepository->save($updatedAccount);

            // events are not responsibility of this method
            event(new AccountBalanceUpdated(
                fromAccount: $fromAccount,
                toAccount: $toAccount,
                amount: $amount,
                transactionType: TransactionType::DEPOSIT->value
            ));
        }
        else if ($transaction->type === TransactionType::WITHDRAWAL) {
            // apply domain rules
            $updatedAccount = $this->withdrawAction->execute($fromAccount, $amount);

            // persist changes
            $this->accountRepository->save($updatedAccount);

            // events are not responsibility of this method
            event(new AccountBalanceUpdated(
                fromAccount: $fromAccount,
                toAccount: $toAccount,
                amount: $amount,
                transactionType: TransactionType::WITHDRAWAL->value
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
                TransactionType::TRANSFER->value
            ));
        }
    }
}
