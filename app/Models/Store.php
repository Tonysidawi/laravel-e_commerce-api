<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Store extends Model
{
    use HasFactory, HasImage;

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'policies' => 'array',
            'category_ids' => 'array',
        ];
    }

    /**
     * Crud methods.
     */
    public static function createStore(array $attributes): self
    {
        $store = new self;
        $store->user_id = auth()->id();
        $store->name = Arr::get($attributes, 'name');
        $store->phone_number = Arr::get($attributes, 'phone_number');
        $store->location = Arr::get($attributes, 'location');
        $store->city = Arr::get($attributes, 'city');
        $store->region = Arr::get($attributes, 'region');
        $store->district = Arr::get($attributes, 'district');
        $store->website = Arr::get($attributes, 'website');
        $store->facebook = Arr::get($attributes, 'facebook');
        $store->instagram = Arr::get($attributes, 'instagram');
        $store->tiktok = Arr::get($attributes, 'tiktok');
        $store->snapchat = Arr::get($attributes, 'snapchat');
        $store->x = Arr::get($attributes, 'x');
        $store->country = Arr::get($attributes, 'country');
        $store->bio = Arr::get($attributes, 'bio');
        $store->is_active = Arr::get($attributes, 'is_active', false);
        $store->is_online = Arr::get($attributes, 'is_online', true);
        $store->policies = Arr::get($attributes, 'policies');
        $store->save();

        Image::makeMany($store, Arr::get($attributes, 'images'));

        return $store->fresh(['images', 'mainImage']);
    }

    public static function updateStore(Store $store, array $attributes): self
    {
        abort_if($store->user_id !== auth()->id(), 403);

        $store->fill($attributes)->save();

        return $store->fresh(['images', 'mainImage']);
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Table relations
     */
    public function loadStoreRelations()
    {
        return $this->load(['images', 'mainImage']);
    }
}
