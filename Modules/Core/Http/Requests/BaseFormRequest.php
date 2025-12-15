<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Core\Constants\HttpStatusConstants;
use Modules\Core\Constants\CoreMessageConstants;
use Modules\Core\Helpers\ApiResponse;
use Override;

abstract class BaseFormRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle failed validation by throwing an HTTP response exception
     * with formatted error response
     *
     * @param  Validator  $validator  The validator instance containing validation errors
     *
     * @throws HttpResponseException
     */
    #[Override]
    protected function failedValidation(Validator $validator): void
    {
        $jsonResponse = ApiResponse::error(
            message: CoreMessageConstants::get(CoreMessageConstants::VALIDATION_FAILED),
            statusCode: HttpStatusConstants::HTTP_422_UNPROCESSABLE_ENTITY,
            data: $validator->errors(),
        );

        throw new HttpResponseException($jsonResponse);
    }
}
