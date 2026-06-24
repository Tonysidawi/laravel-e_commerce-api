<?php

namespace Tests\Feature\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET 'stores'
     */
    public function test_user_get_all_stores_belonging_to_them(): void
    {
        $user = User::factory()
            ->has(Store::factory()->count(3))
            ->create();

        User::factory()
            ->has(Store::factory()->count(2))
            ->create();

        $this->actingAs($user)->getJson('/api/stores')
            ->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment([
                'id' => $user->stores->first()->id,
                'name' => $user->stores->first()->name,
            ]);
    }

    public function test_user_can_get_a_store(): void
    {
        $user = User::factory()->has(Store::factory())->create();

        $this->actingAs($user)->getJson("/api/stores/{$user->stores->first()->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $user->stores->first()->id,
                'name' => $user->stores->first()->name,
                'phone_number' => $user->stores->first()->phone_number,
                'location' => $user->stores->first()->location,
                'city' => $user->stores->first()->city,
                'region' => $user->stores->first()->region,
                'district' => $user->stores->first()->district,
                'website' => $user->stores->first()->website,
                'facebook' => $user->stores->first()->facebook,
                'instagram' => $user->stores->first()->instagram,
                'tiktok' => $user->stores->first()->tiktok,
                'snapchat' => $user->stores->first()->snapchat,
                'x' => $user->stores->first()->x,
                'country' => $user->stores->first()->country,
                'bio' => $user->stores->first()->bio,
                'is_active' => $user->stores->first()->is_active,
                'is_online' => $user->stores->first()->is_online,
            ]);
    }

    /**
     * POST 'stores'
     */
    public function test_user_can_create_a_store(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();

        $data = [
            'name' => 'Test Store',
            'phone_number' => '0244444444',
            'location' => 'Test Location',
            'city' => 'Test City',
            'region' => 'Test Region',
            'district' => 'Test District',
            'website' => 'https://test.com',
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
            'tiktok' => 'https://tiktok.com',
            'snapchat' => 'https://snapchat.com',
            'x' => 'https://x.com',
            'country' => 'Test Country',
            'bio' => 'Test Bio',
            'is_active' => true,
            'is_online' => true,
            'images' => [
                UploadedFile::fake()->image('store-main.jpg'),
            ],
        ];

        $this->actingAs($user)->postJson('/api/stores', $data)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Store created successfully',
                'name' => 'Test Store',
                'phone_number' => '0244444444',
                'location' => 'Test Location',
                'city' => 'Test City',
                'region' => 'Test Region',
                'district' => 'Test District',
                'website' => 'https://test.com',
                'facebook' => 'https://facebook.com',
                'instagram' => 'https://instagram.com',
                'tiktok' => 'https://tiktok.com',
                'snapchat' => 'https://snapchat.com',
                'x' => 'https://x.com',
                'country' => 'Test Country',
                'bio' => 'Test Bio',
                'is_active' => true,
                'is_online' => true,
                'url' => Store::first()->mainImage->url,
                'is_main' => true,
            ]);

        Storage::disk('s3')->assertExists(Store::first()->mainImage->url);
    }

    /**
     * PUT 'stores/{store}'
     */
    public function test_user_can_update_a_store(): void
    {
        $user = User::factory()->has(Store::factory())->create();

        $updateData = [
            'name' => 'Updated Store',
            'phone_number' => '0244444444',
            'location' => 'Test Location',
            'city' => 'Test City',
            'region' => 'Test Region',
            'district' => 'Test District',
        ];

        $this->actingAs($user)->putJson("/api/stores/{$user->stores->first()->id}", $updateData)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Store updated successfully',
                'name' => 'Updated Store',
            ]);
    }

    /**
     * DELETE 'stores/{store}'
     */
    public function test_user_can_delete_a_store(): void
    {
        Storage::fake('s3');

        $user = User::factory()->create();

        $store = Store::factory()
            ->for($user)
            ->hasImages(2)
            ->create();

        $firstImage = $store->images->first();
        $lastImage = $store->images->last();

        // Create fake files
        Storage::disk('s3')->put($firstImage->url, 'content');
        Storage::disk('s3')->put($lastImage->url, 'content');

        $this->assertModelExists($store);
        $this->assertModelExists($firstImage);
        $this->assertModelExists($lastImage);

        Storage::disk('s3')->assertExists($firstImage->url);
        Storage::disk('s3')->assertExists($lastImage->url);

        $this->actingAs($user)
            ->deleteJson("/api/stores/{$store->id}")
            ->assertSuccessful();

        $this->assertModelMissing($store);
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
