<?php

namespace Modules\Notification\Events;

use Modules\Notification\Interfaces\NotifiableEvent;

class LargeTransactionEvent implements NotifiableEvent
{
    private int $accountId;
    private float $amount;
    private string $transactionType;

    public function __construct(int $accountId, float $amount, string $transactionType)
    {
        $this->accountId = $accountId;
        $this->amount = $amount;
        $this->transactionType = $transactionType; // 'deposit', 'withdraw', 'transfer'
    }

    public function getType(): string
    {
        return 'large_transaction';
    }

    public function getData(): array
    {
        return [
            'account_id' => $this->accountId,
            'amount' => $this->amount,
            'transaction_type' => $this->transactionType,
        ];
    }
}
