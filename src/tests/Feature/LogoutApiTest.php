<?php

namespace Tests\Feature;

use App\Http\Middleware\JwtMiddleware;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_auth_logout_unauthorized(): void
    {
        $response = $this->post('/api/auth/logout');
        $response->assertStatus(401);
    }
    public function test_auth_logout_success()
    {
        $user =  User::factory()->create();

        // 2. Generate JWT token
        $token = JWTAuth::fromUser($user);
        $this->withoutMiddleware(
            [
                JwtMiddleware::class
            ]
        );
        // 3. Send request with token in cookie
        $this->withCookie('token', $token);
        $response = $this
            ->post('/api/auth/logout');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logout successful']);
        $response->withoutCookie('token');
        $response->withoutCookie('refresh_token');

        // 4. Assert status or message
        // this work good with postman but not with phpunit because of the cookie
    }
}
