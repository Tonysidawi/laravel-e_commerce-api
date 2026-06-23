<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasImage
{
    public static function bootHasMedia()
    {
        static::deleted(function ($model) {
            Image::deleteImages($model->images);
        });
    }

    /**
     * Relationships.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
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
                'url' => $image['url'],
                'is_main' => $image['is_main'] ?? ! $model->mainImage()->exists(),
            ]);
        }

        $model->setMainImage();
    }
}
