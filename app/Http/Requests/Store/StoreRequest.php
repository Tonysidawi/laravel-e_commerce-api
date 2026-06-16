<?php

namespace App\Http\Requests\Store;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'phone_number' => 'required|string|max:255',
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
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'bio' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'images' => 'required|array',
            'images.*.id' => 'required_with:images|uuid',
            'images.*.key' => 'required_with:images|string|max:1024|regex:/^tmp\/[a-zA-Z0-9_.-]+$/',
            'images.*.bucket' => 'nullable|string|max:255',
            'images.*.name' => 'required_with:images|string|max:255',
            'images.*.content_type' => 'required_with:images|string|max:255',
            'policies' => 'nullable|array',
        ];
    }
}
