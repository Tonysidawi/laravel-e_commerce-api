<?php

namespace App\Http\Requests;

use App\Models\Image;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImageRequest extends FormRequest
{
    public ?Model $model = null;

    public function authorize(): bool
    {
        $this->model = $this->getModel();

        if ($this->model instanceof Store) {
            return auth()->check() && auth()->user()?->isOwnerOfStore($this->model);
        }

        if ($this->model instanceof Product) {
            return auth()->check() && auth()->user()?->isOwnerOfStore($this->model->store);
        }

        return false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_id' => [
                Rule::requiredIf(! $this->input('product_id')),
                Rule::exists('stores', 'id'),
            ],
            'product_id' => [
                Rule::requiredIf(! $this->input('store_id')),
                Rule::exists('products', 'id'),
            ],
            'images' => 'required', 'image', 'mimes:jpeg,png,jpg', 'max:2048',
        ];
    }

    public function makeImage(): Model
    {
        Image::makeMany($this->model, [$this->file('images')]);

        return $this->model;
    }

    public function getModel(): ?Model
    {
        if ($this->input('store_id')) {
            return Store::find($this->input('store_id'));
        }

        if ($this->input('product_id')) {
            return Product::find($this->input('product_id'));
        }

        return null;
    }
}
