<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseFormRequest;
use Modules\Customer\Enums\SupportTicketStatus;

class UpdateSupportTicketRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('isCustomer')|| Gate::allows('isManager')|| Gate::allows('isAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'      => ['required', 'string', Rule::in(SupportTicketStatus::values())],
        ];
    }
}
