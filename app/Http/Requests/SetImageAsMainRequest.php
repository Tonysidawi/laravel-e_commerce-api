<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SetImageAsMainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $image = $this->route('image');

        if ($image->imageable instanceof Store) {
            return auth()->check() && auth()->user()->isOwnerOfStore($image->imageable);
        }

        if ($image->imageable instanceof Product) {
            return auth()->check() && auth()->user()->isOwnerOfStore($image->imageable->store);
        }

        return false;
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
}
