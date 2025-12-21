<?php

namespace Modules\Report\Listeners;

use Modules\Account\Events\AccountBalanceUpdated;
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
            'subject_id' => $event->account->id,
            'description' => sprintf(
                'Balance %s by %s',
                $event->transactionType,
                $event->amount
            ),
            'metadata' => [
                'account_id' => $event->account->id,
                'amount' => $event->amount,
                'transaction_type' => $event->transactionType,
                'from_account_id' => $event->fromAccountId,
                'to_account_id' => $event->toAccountId,
            ],
        ]);
    }
}

