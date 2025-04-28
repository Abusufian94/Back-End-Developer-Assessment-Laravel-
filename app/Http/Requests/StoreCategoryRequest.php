<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust if using specific authorization
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A category with this name already exists.',
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name must not exceed 255 characters.',
        ];
    }
}
