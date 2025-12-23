<?php

namespace Modules\Account\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Http\Resources\UserResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'        => $this->uuid,
            'customer'    => $this->whenLoaded('customer', fn() => new UserResource($this->customer)),
            'account_number' => $this->account_number,
          //  'parent'      => $this->when($this->parent_account_id, new AccountResource($this->parent)),

            'type'        => $this->type->label(),
            'state'       => $this->state->label(),

            'balance'     => number_format((float)$this->balance, 2, '.', ''),
            'currency'    => $this->currency,

            'meta'        => $this->meta,
            'opedned_at'  => $this->opened_at?->toDateString(),

            'created_at'  => $this->created_at?->toDateString(),
            'updated_at'  => $this->updated_at?->toDateString(),

            'children'    => $this->whenLoaded('children', fn() => AccountResource::collection($this->children)),
        ];
    }
}
