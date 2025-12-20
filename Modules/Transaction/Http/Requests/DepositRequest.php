<?php

namespace Modules\Transaction\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Http\Requests\BaseFormRequest;

class DepositRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('isTeller') || Gate::allows('isCustomer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01']
        ];
    }
}
