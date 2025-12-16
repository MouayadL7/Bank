<?php

namespace Modules\Transaction\DTOs;

class TransactionDTO
{
    public function __construct(
        public int $from_account_id,
        public int $to_account_id,
        public float $balance,
        public string $type,
        public string $status,
    ) {}

    /**
     * Create a new DTO instance from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            from_account_id: $data['from_account_id'],
            to_account_id:   $data['to_account_id'],
            balance:         floatval($data['balance']),
            type:            $data['type'],
            status:          $data['status'] ?? 'PENDING',
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'from_account_id' => $this->from_account_id,
            'to_account_id'   => $this->to_account_id,
            'balance'         => $this->balance,
            'type'            => $this->type,
            'status' => $this->status,
        ];
    }
}
