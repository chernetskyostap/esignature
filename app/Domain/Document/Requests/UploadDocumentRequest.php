<?php

namespace App\Domain\Document\Requests;

use App\Domain\Common\Traits\HasValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A file must be uploaded.',
            'file.mimes' => 'Only PDF files are allowed.',
            'file.max' => 'File size must not exceed 2MB.',
        ];
    }
}
