<?php

namespace Modules\AccessControl\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\AccessControl\DTOs\RoleData;
use Modules\Core\Http\Requests\BaseFormRequest;

class StoreRoleRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('isAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('roles', 'name')->whereNull('deleted_at')],
        ];
    }

    public function toDTO(): RoleData
    {
        return RoleData::fromArray($this->validated());
    }
}
