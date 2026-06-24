<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'phone_number' => fake()->phoneNumber(),
            'location' => fake()->address(),
            'city' => fake()->city(),
            'region' => fake()->state(),
            'district' => fake()->city(),
            'website' => fake()->url(),
            'facebook' => fake()->url(),
            'instagram' => fake()->url(),
            'tiktok' => fake()->url(),
            'snapchat' => fake()->url(),
            'x' => fake()->url(),
            'country' => fake()->country(),
            'bio' => fake()->text(),
            'is_active' => fake()->boolean(),
            'is_online' => fake()->boolean(),
        ];
    }

    public function hasImage(int $count = 1): self
    {
        return $this->afterCreating(function (Store $store) use ($count) {
            $images = Image::factory($count)->create([
                'imageable_id' => $store->id,
                'imageable_type' => Store::class,
            ]);

            $images->each(function (Image $image) {
                $imageName = "image{$image->id}";
                $path = UploadedFile::fake()->image($imageName)->storeAs('images/stores', 's3');

                $image->update([
                    'url' => $path,
                    'is_main' => $image->imageable->mainImage()->exists() ? false : true,
                ]);
            });
        });
    }
}
