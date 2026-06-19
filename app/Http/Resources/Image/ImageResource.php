<?php

namespace App\Http\Resources\Image;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'bucket' => $this->bucket,
            'name' => $this->name,
            'content_type' => $this->content_type,
            'url' => $this->bucket && $this->key
                ? Storage::disk('s3')->url($this->key)
                : null,
            'is_main' => $this->is_main,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
