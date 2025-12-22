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
            // User Data
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],

            // Account Data
            'type'              => ['required', 'string', Rule::in(AccountType::values())],
            'balance'           => ['required', 'numeric', 'min:0'],
            'currency'          => ['required', 'string', 'size:3'],
            'parent_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'meta'              => ['required', 'array'],
        ];
    }

    public function toDTO(): AccountData
    {
        return AccountData::fromArray($this->validated());
    }
}
