<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => ['sometimes', 'string', 'max:255',  Rule::unique('products', 'name')->ignore($productId)->whereNull('deleted_at')],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'images' => [
                'nullable',
                'array',
              
            ],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB per image
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists.',
            'images.max' => 'You may upload up to 5 images in total.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be of type: jpeg, png, jpg, or gif.',
            'images.*.max' => 'Each image must not exceed 2MB.',
        ];
    }
}