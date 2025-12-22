<?php

namespace Modules\Report\Listeners;

use Modules\Transaction\Events\AccountBalanceUpdated;
use Modules\Account\Models\Account;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;

class LogAccountBalanceChange
{
    public function __construct(private ReportRepositoryInterface $repository) {}

    public function handle(AccountBalanceUpdated $event): void
    {
        $this->repository->storeAuditLog([
            'event' => 'account.balance.updated',
            'subject_type' => Account::class,
            'subject_id' => $event->fromAccount->id,
            'description' => sprintf(
                'Balance %s by %s',
                $event->transactionType,
                $event->amount
            ),
            'metadata' => [
                'from_account_id' => $event->fromAccount->id,
                'to_account_id' => $event->toAccount->id,
                'amount' => $event->amount,
                'transaction_type' => $event->transactionType,
            ],
        ]);
    }
}

