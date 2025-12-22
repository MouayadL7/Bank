<?php

namespace Modules\Account\DTOs;

use Carbon\Carbon;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;
use Modules\User\DTOs\UserData;

class AccountData
{
    public function __construct(
        public string $uuid,
        public string $accountNumber,
        public string $type,
        public string $state,
        public float $balance,
        public string $currency,
        public array $meta,
        public ?int $parent_account_id,
        public Carbon $openedAt,
        public UserData $userData,
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
            uuid: \Illuminate\Support\Str::uuid()->toString(),
            accountNumber: generateAccountNumber(),
            type: $data['type'] ?? AccountType::SAVINGS->value,
            state: $data['state'] ?? AccountState::ACTIVE->value,
            balance: floatval($data['balance'] ?? 0),
            currency: $data['currency'] ?? 'USD',
            meta: $data['meta'] ?? [],
            parent_account_id: $data['parent_account_id'] ?? null,
            openedAt: now(),
            userData: UserData::fromArray($data),
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
            'uuid'              => $this->uuid,
            'account_number'    => $this->accountNumber,
            'type'              => $this->type,
            'state'             => $this->state,
            'balance'           => $this->balance,
            'currency'          => $this->currency,
            'meta'              => $this->meta,
            'opened_at'         => $this->openedAt,
            'parent_account_id' => $this->parent_account_id,
        ];
    }
}
