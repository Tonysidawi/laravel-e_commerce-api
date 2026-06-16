<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_id' => ['required', 'exists:stores,id'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'details' => ['nullable', 'array'],
            'images' => ['required', 'array'],
            'images.*.id' => ['required_with:images', 'uuid'],
            'images.*.key' => ['required_with:images|string|max:1024|regex:/^tmp\/[a-zA-Z0-9_.-]+$/'],
            'images.*.bucket' => ['nullable|string|max:255'],
            'images.*.name' => ['required_with:images|string|max:255'],
            'images.*.content_type' => ['required_with:images|string|max:255'],
        ];
    }
}
