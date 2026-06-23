<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET 'products'
     */
    public function test_user_can_get_all_products(): void
    {
        $user = User::factory()->create();

        $store = Store::factory()->create([
            'user_id' => $user->id,
        ]);

        Product::factory()->count(10)->create([
            'store_id' => $store->id,
        ]);

        $this->actingAs($user)->getJson('/api/products')
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 10)
                    ->has('data.0.id')
                    ->has('data.0.name')
                    ->etc()
            );
    }

    /**
     * POST 'products'
     */
    public function test_user_can_create_a_product(): void
    {
        $user = User::factory()->create();

        $store = Store::factory()->create([
            'user_id' => $user->id,
        ]);

        $productCategory = ProductCategory::factory()->create();

        $data = [
            'store_id' => $store->id,
            'product_category_id' => $productCategory->id,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'details' => ['color' => 'red', 'size' => 'M'],
            'images' => [
                [
                    'url' => 'tmp/photo.jpg',
                    'is_main' => true,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $data);

        $response
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('message')
                    ->where('message', 'Product created successfully')
                    ->has('data')
                    ->where('data.store_id', $store->id)
                    ->where('data.product_category_id', $productCategory->id)
                    ->where('data.name', 'Test Product')
                    ->where('data.description', 'Test Description')
                    ->where('data.details', ['color' => 'red', 'size' => 'M'])
                    ->has('data.main_image')
                    ->where('data.main_image.url', 'tmp/photo.jpg')
                    ->where('data.main_image.is_main', true)
                    ->has('data.images', 1)
                    ->etc()
            );
    }

    /**
     * PUT 'products/{product}'
     */
    public function test_user_can_update_a_product(): void
    {
        $user = User::factory()->create();

        $store = Store::factory()->create([
            'user_id' => $user->id,
        ]);

        $productCategory = ProductCategory::factory()->create();

        $product = Product::factory()->create([
            'store_id' => $store->id,
            'product_category_id' => $productCategory->id,
        ]);

        $updatedData = [
            'store_id' => $store->id,
            'product_category_id' => $productCategory->id,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'details' => ['color' => 'red', 'size' => 'M'],
            'images' => [[
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'url' => 'tmp/photo.jpg',
                'is_main' => true,
            ]],
        ];

        $this->actingAs($user)->putJson("/api/products/{$product->id}", $updatedData)
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', 'Product updated successfully')
                    ->where('data.id', $product->id)
                    ->where('data.store_id', $store->id)
                    ->where('data.product_category_id', $productCategory->id)
                    ->where('data.name', 'Test Product')
                    ->where('data.description', 'Test Description')
                    ->where('data.price', '100.00')
                    ->where('data.details', ['color' => 'red', 'size' => 'M'])
                    ->has('data.images', 1)
                    ->where('data.images.0.url', 'tmp/photo.jpg')
                    ->where('data.images.0.is_main', true)
                    ->has('data.main_image')
                    ->where('data.main_image.url', 'tmp/photo.jpg')
                    ->where('data.main_image.is_main', true)
                    ->etc()
            );
    }

    /**
     * GET 'products/{product}'
     */
    public function test_user_can_show_a_product(): void
    {
        $user = User::factory()->create();

        $store = Store::factory()->create([
            'user_id' => $user->id,
        ]);

        $productCategory = ProductCategory::factory()->create();

        $product = Product::factory()->create([
            'store_id' => $store->id,
            'product_category_id' => $productCategory->id,
        ]);

        $product->images()->create([
            'url' => 'tmp/photo.jpg',
            'is_main' => true,
        ]);

        $this->actingAs($user)->getJson("/api/products/{$product->id}")
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.id', $product->id)
                    ->where('data.store_id', $store->id)
                    ->where('data.product_category_id', $productCategory->id)
                    ->where('data.name', $product->name)
                    ->where('data.description', $product->description)
                    ->where('data.price', $product->price)
                    ->where('data.details', $product->details)
                    ->has('data.images', 1)
                    ->where('data.images.0.id', $product->images->first()->id)
                    ->where('data.images.0.url', $product->images->first()->url)
                    ->where('data.images.0.is_main', $product->images->first()->is_main)
                    ->where('data.main_image.id', $product->images->first()->id)
                    ->where('data.main_image.url', 'tmp/photo.jpg')
                    ->where('data.main_image.is_main', true)
                    ->etc()
            );
    }

    /**
     * DELETE 'products/{product}'
     */
    public function test_user_can_delete_a_product(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create();

        $this->actingAs($user)->deleteJson("/api/products/{$product->id}")
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', 'Product deleted successfully')
                    ->etc()
            );

        $this->assertModelMissing($product);
    }
}
