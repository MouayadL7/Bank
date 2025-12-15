<?php

namespace Modules\Accounts\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'customer_id' => $this->customer_id,

            'type' => $this->type->label(),

            'state' => $this->state->label(),

            'balance' => number_format((float)$this->balance, 2, '.', ''),
            'currency' => $this->currency,

            'parent' => $this->when($this->parent_account_id, new AccountResource($this->parent)),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
