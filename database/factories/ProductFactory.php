<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}
