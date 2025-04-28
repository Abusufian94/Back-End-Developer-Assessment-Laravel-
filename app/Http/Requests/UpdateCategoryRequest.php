<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($categoryId)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A category with this name already exists.',
            'name.max' => 'The category name must not exceed 255 characters.',
        ];
    }
}