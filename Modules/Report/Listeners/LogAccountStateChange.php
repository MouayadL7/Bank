<?php

namespace Modules\Report\Listeners;

use Modules\Account\Events\AccountStateChanged;
use Modules\Account\Models\Account;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;

class LogAccountStateChange
{
    public function __construct(private ReportRepositoryInterface $repository) {}

    public function handle(AccountStateChanged $event): void
    {
        dd('deposit');
        $this->repository->storeAuditLog([
            'event' => 'account.state.changed',
            'subject_type' => Account::class,
            'subject_id' => $event->account->id,
            'causer_id' => auth()->id(),
            'description' => sprintf('Account state changed to %s', $event->newState),
            'metadata' => [
                'account_id' => $event->account->id,
                'new_state' => $event->newState,
            ],
        ]);
    }
}

