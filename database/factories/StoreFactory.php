<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'country_code' => 'GH',
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'bio' => fake()->text(),
            'is_active' => fake()->boolean(),
            'is_online' => fake()->boolean(),
        ];
    }
}
