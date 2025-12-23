<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Account\Http\Resources\AccountResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'     => $this->uuid,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role->name,
            'status'   => $this->status->value,
            'accounts' => $this->whenLoaded('accounts', fn() => AccountResource::collection($this->accounts))
        ];
    }
}
