<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $image = $this->route('image');
        $owner = $image->imageable;

        if ($owner instanceof Store) {
            return auth()->user()->isOwnerOfStore($owner);
        }

        if ($owner instanceof Product) {
            return auth()->user()->isOwnerOfStore($owner->store);
        }

        return false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
