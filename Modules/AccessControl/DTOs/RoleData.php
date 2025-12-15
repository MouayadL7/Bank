<?php

namespace Modules\AccessControl\DTOs;

use Modules\Core\Http\Requests\BaseFormRequest;

class RoleData
{
    public function __construct() {}

    /**
     * Create a new DTO instance from an array.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static();
    }

    /**
     * Create a new DTO instance from a request.
     *
     * @param BaseFormRequest
     * @return self
     */
    public static function fromRequest(BaseFormRequest $request): self
    {
        return new self();
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
