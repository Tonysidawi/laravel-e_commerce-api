<?php

namespace App\Http\Requests\ProductCategory;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryIndexRequest extends FormRequest
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
        return [];
    }

    public function getProductForACategory(ProductCategory $productCategory)
    {
        return Product::query()
            ->where('product_category_id', $productCategory->id)
            ->with('store', 'productCategory', 'images', 'mainImage')
            ->orderByDesc('created_at')
            ->paginate($this->input('per_page', 10));
    }
}
