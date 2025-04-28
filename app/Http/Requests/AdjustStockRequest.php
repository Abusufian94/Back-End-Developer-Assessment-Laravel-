<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'adjustment' => ['required', 'integer', 'min:-1000', 'max:1000'], // Limit adjustment range
        ];
    }

    public function messages(): array
    {
        return [
            'adjustment.required' => 'The stock adjustment amount is required.',
            'adjustment.integer' => 'The stock adjustment must be an integer.',
            'adjustment.min' => 'The stock adjustment cannot be less than -1000.',
            'adjustment.max' => 'The stock adjustment cannot exceed 1000.',
        ];
    }
}