<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * POST 'users/{user}'
     */
    public function test_user_can_update_profile()
    {
        $user = User::factory()->create([
            'first_name' => 'Maximus',
            'last_name' => 'perez',
            'email' => 'maximus@gmail.com',
            'phone_number' => '0551294465',
            'profile_picture' => 'https://example.com/profile.jpg',
        ]);

        $updateData = [
            'first_name' => 'William',
            'last_name' => 'Walls',
            'email' => 'william@gmail.com',
            'phone_number' => '0244444444',
            'profile_picture' => 'https://example.com/profile1.jpg',
        ];

        $this->actingAs($user)->json('POST', "api/users/{$user->id}", $updateData)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Profile updated successfully',
                'id' => $user->id,
                'first_name' => 'William',
                'last_name' => 'Walls',
                'email' => 'william@gmail.com',
                'phone_number' => '0244444444',
                'profile_picture' => 'https://example.com/profile1.jpg',
            ]);
    }

    /**
     * GET 'user'
     */
    public function test_user_can_show_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('api/user')
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'profile_picture' => $user->profile_picture,
                'status' => $user->status,
                'role' => $user->role,
            ]);
    }

    /**
     * DELETE 'users/{user}'
     */
    public function test_user_can_delete_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete("api/users/{$user->id}")
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'User deleted successfully',
            ]);

        $this->assertModelMissing($user);
    }
}
