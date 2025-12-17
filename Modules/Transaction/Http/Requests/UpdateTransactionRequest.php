<?php

namespace Modules\Transaction\Http\Requests;

use App\Modules\Transaction\Enums\TransactionStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseFormRequest;

class UpdateTransactionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('isTeller') || Gate::allows('isManager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'   => ['required', 'string', Rule::in(TransactionStatus::values())],
        ];
    }
}
