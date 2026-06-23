<?php

namespace App\Traits;

use App\Models\Images;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasImage
{
    public function images(): MorphMany
    {
        return $this->morphMany(Images::class, 'imageable');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Images::class, 'imageable')->where('is_main', true);
    }

    public function setMainImage(): void
    {
        if ($this->images()->where('is_main', true)->exists()) {
            return;
        }

        $this->images()->oldest('id')->first()?->update(['is_main' => true]);
    }

    protected static function syncImages(self $model, array $images): void
    {
        foreach ($images as $image) {
            $model->images()->create([
                'id' => $image['id'],
                'key' => $image['key'],
                'bucket' => $image['bucket'] ?? null,
                'name' => $image['name'],
                'content_type' => $image['content_type'],
                'is_main' => $image['is_main'] ?? false,
            ]);
        }

        $model->setMainImage();
    }
}
