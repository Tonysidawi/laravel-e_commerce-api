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
}
