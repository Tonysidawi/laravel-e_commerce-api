<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'id' => $store->products()->first()->id,
                'name' => $store->products()->first()->name,
            ]);
    }

    /**
     * POST 'products'
     */
    public function test_user_can_create_a_product(): void
    {
        Storage::fake('s3');

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
                UploadedFile::fake()->image('product-main.jpg'),
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $data);

        $response
            ->assertSuccessful()
            ->assertJsonCount(1, 'data.images')
            ->assertJsonFragment([
                'message' => 'Product created successfully',
                'store_id' => $store->id,
                'product_category_id' => $productCategory->id,
                'name' => 'Test Product',
                'description' => 'Test Description',
                'details' => ['color' => 'red', 'size' => 'M'],
                'is_main' => true,
                'url' => Product::first()->mainImage->url,
            ]);

        Storage::disk('s3')->assertExists(Product::first()->mainImage->url);
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
        ];

        $this->actingAs($user)->putJson("/api/products/{$product->id}", $updatedData)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Product updated successfully',
                'id' => $product->id,
                'name' => $updatedData['name'],
                'product_category_id' => $productCategory->id,
                'description' => $updatedData['description'],
                'price' => '100.00',
                'details' => $updatedData['details'],
            ]);
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
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name,
                'product_category_id' => $productCategory->id,
                'description' => $product->description,
                'price' => $product->price,
                'details' => $product->details,
                'url' => $product->mainImage->url,
                'is_main' => true,
            ]);

    }

    /**
     * DELETE 'products/{product}'
     */
    public function test_user_can_delete_a_product(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();

        $product = Product::factory()->hasImages(2)->create();

        $firstImage = $product->images->first();
        $lastImage = $product->images->last();

        Storage::disk('s3')->put($firstImage->url, 'content');
        Storage::disk('s3')->put($lastImage->url, 'content');

        $this->assertModelExists($product);
        $this->assertModelExists($firstImage);
        $this->assertModelExists($lastImage);

        Storage::disk('s3')->assertExists($firstImage->url);
        Storage::disk('s3')->assertExists($lastImage->url);

        $this->actingAs($user)->deleteJson("/api/products/{$product->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Product deleted successfully',
            ]);

        $this->assertModelMissing($product);
        $this->assertDatabaseMissing('images', [
            'id' => $firstImage->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'id' => $lastImage->id,
        ]);
        Storage::disk('s3')->assertMissing($firstImage->url);
        Storage::disk('s3')->assertMissing($lastImage->url);
    }
}
