<?php

namespace App\Http\Requests\Home;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HomePageIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getProducts()
    {
        return Product::query()
            ->with('store', 'productCategory', 'images')
            ->orderByDesc('created_at')
            ->paginate($this->input('per_page', 20));
    }
}
