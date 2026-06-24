<?php

namespace Tests\Feature\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
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
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 3)
                    ->has('data.0.id')
                    ->has('data.0.name')
                    ->etc()
            );
    }

    public function test_user_can_get_a_store(): void
    {
        $user = User::factory()->has(Store::factory())->create();

        $this->actingAs($user)->getJson("/api/stores/{$user->stores->first()->id}")
            ->assertSuccessful()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.id')
                    ->has('data.name')
                    ->etc()
            );
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
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll('success', 'data', 'message')
                    ->where('success', true)
                    ->where('message', 'Store created successfully')
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json
                            ->hasAll(
                                'id',
                                'name',
                                'phone_number',
                                'location',
                                'city',
                                'region',
                                'district',
                                'main_image',
                                'website',
                                'facebook',
                                'instagram',
                                'tiktok',
                                'snapchat',
                                'x',
                                'country',
                                'bio',
                                'is_active',
                                'is_online',
                                'images',
                                'products_count',
                                'services_count',
                                'created_at',
                                'updated_at',
                            )
                            ->etc()
                    )
            );

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
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', 'Store updated successfully')
                    ->where('data.name', 'Updated Store')
                    ->etc()
            );
    }

    /**
     * DELETE 'stores/{store}'
     */
    public function test_user_can_delete_a_store(): void
    {
        $user = User::factory()->has(Store::factory())->create();
        $store = $user->stores->first();

        $this->actingAs($user)->deleteJson("/api/stores/{$store->id}")
            ->assertSuccessful()
            ->assertJson([
                'message' => 'Store deleted successfully',
            ]);

        $this->assertModelMissing($store);
    }
}
