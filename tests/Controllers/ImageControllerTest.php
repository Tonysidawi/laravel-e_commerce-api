<?php

namespace Tests\Feature\Controllers;

use App\Models\Image;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');
    }

    /**
     * POST 'images'
     */
    public function test_user_can_upload_image_to_store(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->for($user)->hasImages(3)->create();

        $this->assertCount(3, Image::all());
        $this->assertCount(3, $store->images);

        $this->actingAs($user)->post('/api/images', [
            'store_id' => $store->id,
            'image' => UploadedFile::fake()->image('store-photo.jpg'),
        ], ['Accept' => 'application/json'])
            ->assertSuccessful();

        $this->assertCount(4, Image::all());
        $this->assertCount(4, $store->refresh()->images);
    }

    /**
     * POST 'images'
     */
    public function test_user_can_upload_image_to_product(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->for($store)->hasImages(4)->create();

        $this->actingAs($user)->post('/api/images', [
            'product_id' => $product->id,
            'image' => UploadedFile::fake()->image('product-photo.jpg'),
        ], ['Accept' => 'application/json'])->assertSuccessful();

        $this->assertCount(5, Image::all());
        $this->assertCount(5, $product->refresh()->images);
        $this->assertTrue($product->refresh()->images->last()->is_main);
    }

    /**
     * DELETE 'images/{image}'
     */
    public function test_user_can_delete_uploaded_image(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->image('delete-me.jpg');

        $this->actingAs($user)->post('/api/images', [
            'store_id' => $store->id,
            'image' => $file,
        ], ['Accept' => 'application/json'])->assertSuccessful();

        $image = Image::first();
        $path = $image->key;

        $this->actingAs($user)->deleteJson("/api/images/{$image->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Image deleted successfully',
            ]);

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('s3')->assertExists($path);
    }

    /**
     * POST 'images'
     */
    public function test_user_cannot_upload_image_to_another_users_store(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)->post('/api/images', [
            'store_id' => $store->id,
            'image' => UploadedFile::fake()->image('photo.jpg'),
        ], ['Accept' => 'application/json'])
            ->assertForbidden();

        $this->assertDatabaseCount('images', 0);
    }

    /**
     * POST 'images/{image}/main'
     */
    public function test_uploaded_store_image_appears_as_main_image_with_s3_url(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post('/api/images', [
            'store_id' => $store->id,
            'image' => UploadedFile::fake()->image('store-main.jpg'),
        ], ['Accept' => 'application/json'])->assertSuccessful();

        $image = Image::first();

        $this->actingAs($user)->getJson("/api/stores/{$store->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $store->id,
                'name' => $store->name,
                'url' => $image->url,
                'is_main' => true,
            ]);
    }

    /**
     * POST 'images/{image}/main'
     */
    public function test_uploaded_product_image_appears_as_main_image_with_s3_url(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['store_id' => $store->id]);

        $this->actingAs($user)->post('/api/images', [
            'product_id' => $product->id,
            'image' => UploadedFile::fake()->image('product-main.jpg'),
        ], ['Accept' => 'application/json'])->assertSuccessful();

        $image = Image::first();

        $this->actingAs($user)->getJson("/api/products/{$product->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $image->id,
                'url' => $image->url,
                'is_main' => true,
            ]);
    }

    /**
     * POST 'images/{image}/main'
     */
    public function test_a_user_can_set_an_image_as_main()
    {
        $user = User::factory()->create();

        // user store
        $store = Store::factory()->for($user)->hasImage(3)->create();

        // user store products
        $product = Product::factory()->for($store)->hasImage(4)->create();

        // assert Images exists
        $this->assertCount(7, Image::all());

        // assert store has main image
        $this->assertTrue($store->mainImage()->exists());
        $this->assertEquals($store->images->first()->id, $store->mainImage()->first()->id);

        // assert product has main image
        $this->assertTrue($product->mainImage()->exists());
        $this->assertEquals($product->images->first()->id, $product->mainImage()->first()->id);

        // api to set store last image as main
        $this->actingAs($user)->postJson("/api/images/{$store->images->last()->id}/main")
            ->assertSuccessful();

        // assert store main image changed
        $this->assertEquals($store->images->last()->id, $store->mainImage()->first()->id);

        // api to set product last image as main
        $this->actingAs($user)->postJson("/api/images/{$product->images->last()->id}/main")
            ->assertSuccessful();

        // assert product main image changed
        $this->assertEquals($product->images->last()->id, $product->mainImage()->first()->id);
    }

    /**
     * DELETE 'images/{image}'
     */
    public function test_a_user_can_delete_an_image_from_store()
    {
        $user = User::factory()->create();

        $store = Store::factory()->for($user)->hasImage(3)->create();

        $images = $store->images;

        $this->assertCount(3, Image::all());

        $this->actingAs($user)->deleteJson("/api/images/{$images->first()->id}")
            ->assertSuccessful();

        $this->assertCount(2, Image::all());
    }

    /**
     * DELETE 'images/{image}'
     */
    public function test_a_user_can_delete_an_image_from_product()
    {
        $user = User::factory()->create();

        $store = Store::factory()->for($user)->hasImage(3)->create();

        $product = Product::factory()->for($store)->hasImage(4)->create();

        $images = $product->images;

        $this->assertCount(7, Image::all());

        $this->actingAs($user)->deleteJson("/api/images/{$images->first()->id}")
            ->assertSuccessful();

        $this->assertCount(6, Image::all());
    }
}
