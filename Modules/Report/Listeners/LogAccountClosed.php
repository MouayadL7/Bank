<?php

namespace Modules\Report\Listeners;

use Modules\Account\Events\AccountClosed;
use Modules\Account\Models\Account;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;

class LogAccountClosed
{
    public function __construct(private ReportRepositoryInterface $repository) {}

    public function handle(AccountClosed $event): void
    {
        $this->repository->storeAuditLog([
            'event' => 'account.closed',
            'subject_type' => Account::class,
            'subject_id' => $event->account->id,
            'description' => 'Account closed',
            'metadata' => [
                'account_id' => $event->account->id,
            ],
        ]);
    }
}

