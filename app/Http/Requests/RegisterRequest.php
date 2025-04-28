<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow anyone to register
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]+$/'
            ],
            'confirm_password' => ['required', 'same:password'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.regex' => 'The password must contain at least one letter, one number, and one special character (!@#$%^&*).',
            'confirm_password.required' => 'The confirmation password is required.',
            'confirm_password.same' => 'The confirmation password does not match.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The role must be either admin or customer.',
        ];
    }
}