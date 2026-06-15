<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'store_id',
        'key',
        'bucket',
        'name',
        'content_type',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
