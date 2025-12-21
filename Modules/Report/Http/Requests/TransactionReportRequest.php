<?php

namespace Modules\Report\Http\Requests;

use App\Modules\Transaction\Enums\TransactionStatus;
use App\Modules\Transaction\Enums\TransactionType;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseFormRequest;

class TransactionReportRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'status' => ['nullable', 'string', Rule::in(TransactionStatus::values())],
            'type' => ['nullable', 'string', Rule::in(TransactionType::values())],
        ];
    }
}

