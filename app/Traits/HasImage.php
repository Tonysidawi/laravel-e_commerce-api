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

    public function mainImageUrl(): ?string
    {
        return $this->mainImage()?->first()?->url;
    }

    public function setMainImage(): void
    {
        if (! $this->mainImage && $this->images()->count() > 0) {
            $this->refresh()->images()->first()->update(['is_main' => true]);
        }
    }
}
