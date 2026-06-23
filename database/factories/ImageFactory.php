<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageable = fake()->randomElement([Store::class, Product::class]);

        return [
            'imageable_id' => $imageable::factory(),
            'imageable_type' => $imageable,
            'url' => 'images/'.fake()->uuid().'.jpg',
        ];
    }
}
