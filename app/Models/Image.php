<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public const IMAGE_FOLDER = 'images';

    /**
     * Crud methods
     */
    public static function makeMany(Model $model, ?array $images): void
    {
        if (! $images) {
            return;
        }

        $modelName = Str::of(class_basename($model))->lower()->append('s');
        $folder = self::IMAGE_FOLDER;

        foreach ($images as $image) {
            $path = Storage::disk('s3')->put($folder."/{$modelName}", $image);

            $model->images()->create([
                'url' => $path,
                'is_main' => ! $model->mainImage()->exists(),
            ]);
        }

        // set first image media as main image if not already set
        $model->setMainImage();
    }

    public function deleteImage(): void
    {
        // delete image from s3
        Storage::disk('s3')->delete($this->url);

        // delete image from database
        $this->delete();
    }

    public static function deleteImages(Collection $images): void
    {
        $ids = $images->pluck('id')->toArray();
        $urls = $images->pluck('url')->toArray();

        // delete images from s3
        Storage::disk('s3')->delete($urls);

        // delete images from database
        self::destroy($ids);
    }

    /**
     * Mutators
     */
    public function setAsMain(): void
    {
        $this->imageable->images()->update(['is_main' => false]);

        $this->update(['is_main' => true]);
    }

    /**
     * Relationships
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
