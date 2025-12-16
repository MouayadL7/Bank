<?php

namespace Modules\Account\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\Account\DTOs\AccountData;
use Modules\Account\Enums\AccountType;
use Modules\Core\Http\Requests\BaseFormRequest;

class CreateAccountRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('isTeller');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', Rule::in(AccountType::values())],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:0'],
            'parent_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function toDTO(): AccountData
    {
        return AccountData::fromArray($this->validated());
    }
}
    