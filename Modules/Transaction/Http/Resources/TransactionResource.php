<?php

namespace Modules\Transaction\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from_account_id' => $this->from_account_id,
            'to_account_id'   => $this->to_account_id,
            'balance'          => $this->amount,
            'type'            => $this->type,
            'status'          => $this->status,
            'created_at'      => $this->created_at,
        ];
    }
}
