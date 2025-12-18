<?php

namespace Modules\AccessControl\DTOs;

class RoleData
{
    public function __construct(public string $name) {}

    /**
     * Create a new DTO instance from an array.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name']
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
            'name' => $this->name,
        ];
    }
}
