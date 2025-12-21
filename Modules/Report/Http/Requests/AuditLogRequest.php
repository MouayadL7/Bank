<?php

namespace Modules\Report\Http\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class AuditLogRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'event' => 'nullable|string|max:150',
            'subject_type' => 'nullable|string|max:150',
            'subject_id' => 'nullable|integer',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'limit' => 'nullable|integer|min:1|max:500',
        ];
    }
}

