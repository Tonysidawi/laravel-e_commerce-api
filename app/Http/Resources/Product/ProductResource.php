<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Image\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'store_id' => $this->store_id,
            'product_category_id' => $this->product_category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'details' => $this->details,
            'images' => ImageResource::collection($this->whenLoaded('images', fn () => $this->images)),
            'created_at' => $this->created_at,
        ];
    }
}
