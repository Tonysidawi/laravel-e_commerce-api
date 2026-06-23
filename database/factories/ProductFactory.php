<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'product_category_id' => ProductCategory::factory(),
            'name' => fake()->name(),
            'description' => fake()->text(),
            'price' => fake()->randomFloat(2, 0, 1000),
            'details' => [
                'color' => fake()->colorName(),
                'size' => fake()->randomElement(['S', 'M', 'L']),
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function hasImage(int $count = 1): self
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            $images = Image::factory($count)->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class,
            ]);

            $images->each(function (Image $image) {
                $imageName = "image{$image->id}";
                $path = UploadedFile::fake()->image($imageName)->storeAs('images/stores', $imageName, config('filesystems.default'));

                $image->update([
                    'url' => $path,
                    'is_main' => $image->imageable->mainImage()->exists() ? false : true,
                ]);
            });
        });
    }
}
