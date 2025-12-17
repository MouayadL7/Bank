<?php

namespace Modules\Transaction\DTOs;

use App\Modules\Transaction\Enums\TransactionStatus;
use App\Modules\Transaction\Enums\TransactionType;
use DateTimeInterface;

class TransactionDTO
{
    public function __construct(
        public ?int $from_account_id,
        public int $to_account_id,
        public float $amount,
        public TransactionType $type,
        public bool $is_scheduled = false,
        public ?DateTimeInterface $scheduled_at = null,
        public TransactionStatus $status = TransactionStatus::PENDING,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            from_account_id: $data['from_account_id'] ?? null,
            to_account_id:   $data['to_account_id'],
            amount:          (float) $data['amount'],
            type:            TransactionType::from($data['type']),
            is_scheduled:    $data['is_scheduled'] ?? false,
            scheduled_at:    isset($data['scheduled_at'])
                ? new \DateTime($data['scheduled_at'])
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'from_account_id' => $this->from_account_id,
            'to_account_id'   => $this->to_account_id,
            'amount'          => $this->amount,
            'type'            => $this->type->value,
            'status'          => $this->status->value,
            'is_scheduled'    => $this->is_scheduled,
            'scheduled_at'    => $this->scheduled_at,
        ];
    }
}
