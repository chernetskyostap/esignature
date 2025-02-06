<?php

namespace App\Domain\Document\Requests;

use App\Domain\Common\Traits\HasValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SignatureSendRequest extends FormRequest
{
    use HasValidationHandler;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],
        ];
    }
}
