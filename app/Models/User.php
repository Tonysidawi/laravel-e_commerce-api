<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Crud methods.
     */
    public static function createUser(array $attributes)
    {
        $user = new self;
        $user->first_name = Arr::get($attributes, 'first_name');
        $user->last_name = Arr::get($attributes, 'last_name');
        $user->email = Arr::get($attributes, 'email');
        $user->password = Arr::get($attributes, 'password');
        $user->phone_number = Arr::get($attributes, 'phone_number');
        $user->profile_picture = Arr::get($attributes, 'profile_picture');
        $user->status = Arr::get($attributes, 'status', 'active');
        $user->role = Arr::get($attributes, 'role', 'user');
        $user->save();

        return $user;
    }

    public static function updateUser(array $data): self
    {
        $user = auth()->user();

        $user->fill($data)->save();

        return $user;
    }

    /**
     * Relationships
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Store::class);
    }

    public function isOwnerOfStore(?Store $store): bool
    {
        return $store !== null && $this->id === $store->user_id;
    }
}
