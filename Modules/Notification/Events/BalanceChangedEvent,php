<?php

namespace Modules\Notification\Events;

use Modules\Notification\Interfaces\NotifiableEvent;


class BalanceChangedEvent implements NotifiableEvent
{
    private int $accountId;
    private float $oldBalance;
    private float $newBalance;

    public function __construct(
        int $accountId,
        float $oldBalance,
        float $newBalance
    ) {
        $this->accountId   = $accountId;
        $this->oldBalance  = $oldBalance;
        $this->newBalance  = $newBalance;
    }

    public function getType(): string
    {
        return 'balance_changed';
    }

    public function getData(): array
    {
        return [
            'account_id'   => $this->accountId,
            'old_balance'  => $this->oldBalance,
            'new_balance'  => $this->newBalance,
        ];
    }
}
