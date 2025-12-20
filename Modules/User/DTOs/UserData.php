<?php

namespace Modules\User\DTOs;

class UserData
{
    public function __construct(
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
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            roleId: $data['role_id'],
            status: $data['status'],
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
            'uuid'     => \Illuminate\Support\Str::uuid()->toString(),
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'role_id'  => $this->roleId,
            'status'   => $this->status,
        ];
    }
}
