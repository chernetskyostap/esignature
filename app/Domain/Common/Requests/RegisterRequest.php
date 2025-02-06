<?php

namespace App\Domain\Common\Requests;

use App\Domain\Common\DTO\RegisterDto;
use App\Domain\Common\Traits\HasValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:30',
            ],
            'email' => [
                'required',
                'string',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ]
        ];
    }

    public function toDto(): RegisterDto
    {
        return new RegisterDto(
            name: $this->get('name'),
            email: $this->get('email'),
            password: $this->get('password'),
        );
    }
}
