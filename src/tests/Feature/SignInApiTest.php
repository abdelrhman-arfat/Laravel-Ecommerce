<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignInApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_signin_unauthorized(): void
    {
        $response = $this->post('/api/auth/signin', [
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(401);
        $response->assertJson(['data' => null, 'message' => 'Invalid credentials']);
    }
    public function test_auth_signin_authorized(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => Hash::make('12345678')]);

        $response = $this->post('/api/auth/signin', [
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'message']);
        $response->assertJson(['message' => 'Login successful']);
        $response->assertCookie('token');
        $response->assertCookie('refresh_token');
    }
    public function test_auth_signin_bad_request_email(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => Hash::make('12345678')]);

        $response = $this->post('/api/auth/signin', [
            'email' => 'abdo',
            'password' => '12345678'
        ]);

        $response->assertStatus(400);
    }
    public function test_auth_signin_bad_request_password(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => Hash::make('12345678')]);

        $response = $this->post('/api/auth/signin', [
            'email' => 'a@b.com',
            'password' => '1234567'
        ]);

        $response->assertStatus(400);
    }
    public function test_auth_signin_invalid_password(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => Hash::make('123456789')]);

        $response = $this->post('/api/auth/signin', [
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(401);
        $response->assertJson(['data' => null, 'message' => 'Invalid credentials']);
    }
    public function test_auth_signin_invalid_email(): void
    {
        User::factory()->create(['email' => 'a@bb.com', 'password' => Hash::make('12345678')]);

        $response = $this->post('/api/auth/signin', [
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(401);
        $response->assertJson(['data' => null, 'message' => 'Invalid credentials']);
    }
}
