<?php

namespace Tests\Feature\Controllers;

use App\Models\Images;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');
    }

    public function test_user_can_upload_image_to_store(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->image('store-photo.jpg');

        $this->actingAs($user)->post('/api/images', [
            'store_id' => $store->id,
            'images' => $file,
        ], ['Accept' => 'application/json'])
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', 'Image uploaded successfully')
                    ->has('data.id')
                    ->where('data.name', 'store-photo.jpg')
                    ->where('data.content_type', 'image/jpeg')
                    ->where('data.is_main', true)
                    ->has('data.key')
                    ->has('data.url')
                    ->etc()
            );

        $image = Images::first();

        $this->assertNotNull($image);
        $this->assertSame($store->id, $image->imageable_id);
        $this->assertSame(Store::class, $image->imageable_type);
        Storage::disk('s3')->assertExists($image->key);
    }

    public function test_user_can_upload_image_to_product(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);
        $file = UploadedFile::fake()->image('product-photo.png');

        $this->actingAs($user)->post('/api/images', [
            'product_id' => $product->id,
            'images' => $file,
        ], ['Accept' => 'application/json'])
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', 'Image uploaded successfully')
                    ->has('data.id')
                    ->where('data.name', 'product-photo.png')
                    ->where('data.is_main', true)
                    ->etc()
            );

        $this->assertDatabaseHas('images', [
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
            'name' => 'product-photo.png',
        ]);
    }

    public function test_user_can_delete_uploaded_image(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->image('delete-me.jpg');

        $this->actingAs($user)->post('/api/images', [
            'store_id' => $store->id,
            'images' => $file,
        ], ['Accept' => 'application/json'])->assertSuccessful();

        $image = Images::first();
        $path = $image->key;

        $this->actingAs($user)->deleteJson("/api/images/{$image->id}")
            ->assertSuccessful()
            ->assertJson([
                'message' => 'Image deleted successfully',
            ]);

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('s3')->assertMissing($path);
    }

    public function test_user_cannot_upload_image_to_another_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)->post('/api/images', [
            'store_id' => $store->id,
            'images' => UploadedFile::fake()->image('photo.jpg'),
        ], ['Accept' => 'application/json'])
            ->assertForbidden();

        $this->assertDatabaseCount('images', 0);
    }
}
