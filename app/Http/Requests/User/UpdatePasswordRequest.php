<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use App\Rules\ConfirmPassword;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'required_with:password_confirmation|string|confirmed',
            'current_password' => [
                'nullable',
                new ConfirmPassword($this->id, $this->current_password)
            ],
            'password_confirmation' => 'required',
        ];
    }
}
