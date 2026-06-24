<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\Image\ImageResource;
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
            'main_image' => $this->when(
                $this->relationLoaded('mainImage') && $this->mainImage !== null,
                fn () => new ImageResource($this->mainImage)
            ),
            'website' => $this->website,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'snapchat' => $this->snapchat,
            'x' => $this->x,
            'country' => $this->country,
            'bio' => $this->bio,
            'is_active' => $this->is_active,
            'is_online' => $this->is_online,
            'policies' => $this->policies,
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
