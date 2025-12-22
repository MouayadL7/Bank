<?php

namespace Modules\User\DTOs;

use Illuminate\Support\Facades\Hash;
use Modules\AccessControl\Models\Role;
use Modules\User\Enums\UserStatus;

class UserData
{
    public function __construct(
        public string $uuid,
        public string $name,
        public string $email,
        public string $password,
        public int $roleId,
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
            uuid: \Illuminate\Support\Str::uuid()->toString(),
            name: $data['name'],
            email: $data['email'],
            password: '12345678', //generateStrongPassword(),
            roleId: $data['role_id'] ?? Role::ROLE_CUSTOMER,
            status: $data['status'] ?? UserStatus::ACTIVE->value,
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
            'uuid'     => $this->uuid,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role_id'  => $this->roleId,
            'status'   => $this->status,
        ];
    }
}
