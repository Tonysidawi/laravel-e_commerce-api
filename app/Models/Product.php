<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Product extends Model
{
    use HasFactory;
    use HasImage;

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

        Image::makeMany($product, Arr::get($attributes, 'images'));

        return $product->fresh(['images', 'mainImage']);
    }

    public static function updateProduct(Product $product, array $attributes): self
    {
        abort_if($product->store->user_id !== auth()->id(), 403);

        $product->fill($attributes)->save();

        return $product->fresh(['images', 'mainImage']);
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
            'mainImage',
        ]);
    }
}
