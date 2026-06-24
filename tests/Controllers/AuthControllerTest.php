<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * POST 'auth/register'
     */
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
            ->assertJsonFragment([
                'first_name' => 'Maximus',
                'last_name' => 'Perez',
                'email' => 'maximus.perez@gmail.com',
                'phone_number' => '0244444444',
            ]);

    }

    /**
     * POST 'auth/login'
     */
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
            ->assertJsonFragment([
                'email' => $user->email,
            ]);

        $this->assertTrue(Auth::check());
    }

    /**
     * POST 'auth/logout'
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertTrue(Auth::check());

        $this->json('POST', '/api/auth/logout')
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Logged out successfully',
            ]);
    }

    /**
     * POST 'auth/reset-password'
     */
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
            ->assertJsonFragment([
                'message' => 'Password reset successful',
            ]);

        $this->assertTrue(Hash::check('new_password', $user->refresh()->password));
        $this->assertFalse(Hash::check('old_password', $user->refresh()->password));
    }
}
