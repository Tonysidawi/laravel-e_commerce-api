<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // post
    public function test_user_can_register()
    {
        $data = [
            'first_name' => 'Maximus',
            'last_name' => 'Perez',
            'email' => 'maximus.perez@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '0244444444',
            'profile_picture' => 'https://example.com/profile.jpg',
            'status' => 'active',
            'role' => 'user',
        ];

        $this->json('POST', '/api/auth/register', $data)
            ->assertSuccessful()
            ->assertJson(
                fn(AssertableJson $json) => $json->has('id')
                    ->has('first_name')
                    ->has('last_name')
                    ->has('email')
                    ->has('phone_number')
                    ->has('profile_picture')
                    ->has('status')
                    ->has('role')
                    ->has('token')
            );
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create();

        $this->assertFalse(Auth::check());

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $this->json('POST', '/api/auth/login', $data)
            ->assertSuccessful()
            ->assertJson([
                'email' => $user->email,
            ]);

        $this->assertTrue(Auth::check());
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertTrue(Auth::check());

        $this->json('POST', '/api/auth/logout')
            ->assertSuccessful()
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);
    }

    public function test_user_can_reset_password()
    {
        $user = User::factory()->create(['password' => 'old_password']);

        $this->assertTrue(Hash::check('old_password', $user->password));

        $this->assertFalse(Hash::check('secret', $user->password));

        $this->json('POST', '/api/auth/reset-password', [
            'email' => $user->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ])->assertSuccessful()
            ->assertJson([
                'message' => 'Password reset successful',
            ]);

        $this->assertTrue(Hash::check('new_password', $user->refresh()->password));
        $this->assertFalse(Hash::check('old_password', $user->refresh()->password));
    }
}
