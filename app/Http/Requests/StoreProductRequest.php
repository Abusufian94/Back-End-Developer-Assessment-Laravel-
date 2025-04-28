<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255',Rule::unique('products', 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'images.max' => 'You may upload up to 5 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be of type: jpeg, png, jpg, or gif.',
            'images.*.max' => 'Each image must not exceed 2MB.',
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.max' => 'The product name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price cannot be negative.',
            'price.max' => 'The price cannot exceed 999,999.99.',
            'stock_quantity.required' => 'The stock quantity is required.',
            'stock_quantity.integer' => 'The stock quantity must be an integer.',
            'stock_quantity.min' => 'The stock quantity cannot be negative.',
            'stock_quantity.max' => 'The stock quantity cannot exceed 100,000.',
            'images.array' => 'The images must be an array.',
            'images.max' => 'You may upload up to 5 images.',
            'images.*.file' => 'Each image must be a valid file.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be of type: jpeg, png, or jpg.',
            'images.*.max' => 'Each image cannot exceed 2MB.',
            'category_ids.array' => 'The category IDs must be an array.',
            'category_ids.*.integer' => 'Each category ID must be an integer.',
            'category_ids.*.exists' => 'One or more category IDs are invalid.',
        ];
    }
}