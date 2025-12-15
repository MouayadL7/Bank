<?php

namespace Modules\Account\DTOs;

use Modules\Account\Enums\AccountType;

class AccountData
{
    public function __construct(
        public string $uuid,
        public ?int $customer_id,
        public string $type,
        public float $balance,
        public string $currency,
        public ?int $parent_account_id,
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
            uuid: $data['uuid'] ?? \Illuminate\Support\Str::uuid()->toString(),
            customer_id: $data['customer_id'] ?? null,
            type: $data['type'] ?? AccountType::SAVINGS->value,
            balance: floatval($data['balance'] ?? 0),
            currency: $data['currency'] ?? 'USD',
            parent_account_id: $data['parent_account_id'] ?? null,
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
            'uuid' => $this->uuid,
            'customer_id' => $this->customer_id,
            'type' => $this->type,
            'balance' => $this->balance,
            'currency' => $this->currency,
            'parent_account_id' => $this->parent_account_id,
        ];
    }
}
