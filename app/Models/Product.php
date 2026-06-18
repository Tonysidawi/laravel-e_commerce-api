<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_category_id',
        'name',
        'description',
        'details',
        'price',
    ];

    protected $casts = [
        'details' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * CRUD
     */
    public static function createProduct(array $attributes): self
    {
        $product = new self;
        $product->store_id = Arr::get($attributes, 'store_id');
        $product->product_category_id = Arr::get($attributes, 'product_category_id');
        $product->name = Arr::get($attributes, 'name');
        $product->description = Arr::get($attributes, 'description');
        $product->details = Arr::get($attributes, 'details');
        $product->price = Arr::get($attributes, 'price');
        $product->save();

        static::syncImages($product, Arr::get($attributes, 'images', []));

        return $product->fresh(['images']);
    }

    public static function updateProduct(Product $product, array $attributes): self
    {
        abort_if($product->store->user_id !== auth()->id(), 403);

        $product->fill(Arr::only($attributes, [
            'name',
            'description',
            'price',
            'details',
            'product_category_id',
        ]))->save();

        if (array_key_exists('images', $attributes)) {
            $product->images()->delete();
            static::syncImages($product, $attributes['images']);
        }

        return $product->fresh(['images']);
    }

    protected static function syncImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $product->images()->create([
                'id' => $image['id'],
                'key' => $image['key'],
                'bucket' => $image['bucket'] ?? null,
                'name' => $image['name'],
                'content_type' => $image['content_type'],
            ]);
        }
    }

    /**
     * Table relations
     */
    public function loadProductRelations()
    {
        return $this->load([
            'store',
            'productCategory',
            'images',
        ]);
    }

    /**
     * Relationships
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Images::class, 'imageable');
    }
}
