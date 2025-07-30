<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_create_access_token(): void
    {
        $user = User::factory()->create();
        $token = JwtService::generateToken($user);

        JWTAuth::setToken($token);
        $this->assertTrue(JWTAuth::check());
        $this->assertNotNull($token);
    }

    public function test_create_refresh_token(): void
    {
        $user = User::factory()->create();
        $token = JwtService::generateRefreshToken($user);

        JWTAuth::setToken($token);
        $this->assertTrue(JWTAuth::check());
        $this->assertNotNull($token);
    }

    public function test_invalidate_token(): void
    {
        $user = User::factory()->create();
        $token = JwtService::generateToken($user);

        JWTAuth::setToken($token);
        $this->assertTrue(JWTAuth::check());

        JwtService::invalidateTokenInCookie($token);

        JWTAuth::setToken($token);
        $this->assertFalse(JWTAuth::check());
    }
}
