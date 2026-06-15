<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'location' => $this->location,
            'city' => $this->city,
            'region' => $this->region,
            'district' => $this->district,
            'website' => $this->website,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'snapchat' => $this->snapchat,
            'x' => $this->x,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'bio' => $this->bio,
            'is_active' => $this->is_active,
            'is_online' => $this->is_online,
            'images' => ImageReource::collection($this->whenLoaded('images', fn () => $this->images)),
            'products_count' => $this->products_count ?? 0,
            'services_count' => $this->services_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
