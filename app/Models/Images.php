<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Images extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public const IMAGE_FOLDER = 'images';

    public static function upload(Model $model, UploadedFile $file): self
    {
        $disk = 's3';
        $folder = self::IMAGE_FOLDER.'/'.Str::plural(Str::lower(class_basename($model)));
        $path = Storage::disk($disk)->put($folder, $file);

        return $model->images()->create([
            'id' => (string) Str::uuid(),
            'key' => $path,
            'bucket' => config('filesystems.disks.s3.bucket'),
            'name' => $file->getClientOriginalName(),
            'content_type' => $file->getMimeType() ?? 'image/jpeg',
            'is_main' => ! $model->images()->where('is_main', true)->exists(),
        ]);
    }

    public function deleteImage(): void
    {
        if ($this->key) {
            Storage::disk('s3')->delete($this->key);
        }

        $this->delete();
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
