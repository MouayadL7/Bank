<?php

namespace Modules\Report\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;
use Modules\Core\Http\Requests\BaseFormRequest;

class AccountSummaryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', Rule::in(AccountType::values())],
            'state' => ['nullable', 'string', Rule::in(AccountState::values())],
            'min_balance' => 'nullable|numeric',
            'max_balance' => 'nullable|numeric|gte:min_balance',
            'opened_from' => 'nullable|date',
            'opened_to' => 'nullable|date|after_or_equal:opened_from',
        ];
    }
}

