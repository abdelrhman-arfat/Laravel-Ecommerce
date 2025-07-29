<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SignUpApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_signup_success(): void
    {
        Mail::fake();

        $response = $this->post('/api/auth/signup', [
            'name' => 'abdo',
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data', 'message']);
        $response->assertJson(['message' => 'User created successfully']);
        $response->assertCookie('token');
        $response->assertCookie('refresh_token');
    }
    public function test_auth_signup_bad_request_password(): void
    {
        $response = $this->post('/api/auth/signup', [
            'name' => 'abdo',
            'email' => 'a@b.com',
            'password' => '1234567'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['data' => null, 'message' => 'The password field must be at least 8 characters.']);
    }
    public function test_auth_signup_bad_request_email(): void
    {
        Mail::fake();

        $response = $this->post('/api/auth/signup', [
            'name' => 'abdo',
            'email' => 'abdo',
            'password' => '12345678'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['data' => null, 'message' => 'The email field must be a valid email address.']);
    }
    public function test_auth_signup_bad_request_name(): void
    {
        Mail::fake();

        $response = $this->post('/api/auth/signup', [
            'name' => '',
            'email' => 'abdo',
            'password' => '12345678'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['data' => null, 'message' => 'The name field is required.']);
    }
    public function test_auth_signup_user_exists(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'a@b.com', 'password' => Hash::make('12345678')]);

        $response = $this->post('/api/auth/signup', [
            'name' => 'abdo',
            'email' => 'a@b.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['data' => null, 'message' => 'The email field must be unique.']);
    }
}
