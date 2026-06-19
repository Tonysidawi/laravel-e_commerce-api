<?php

namespace App\Http\Requests\Store;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateResquest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
        && $this->store->user_id === auth()->id();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|min:3|max:255',
            'phone_number' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'snapchat' => 'nullable|string|max:255',
            'x' => 'nullable|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'country_code' => 'sometimes|required|string|size:2',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'bio' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'images' => 'sometimes|array',
            'images.file' => 'nullable|file|max:2048',
            'policies' => 'nullable|array',
        ];
    }
}
