<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for tests

    }

    public function test_user_api_get_all_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this
            ->withoutMiddleware([
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ])
            ->get('/api/users');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => ['id', 'name', 'email', 'role', 'is_active']
            ]
        ]);
    }

    public function test_user_api_get_user_by_id(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->withoutMiddleware([
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ])
            ->get("/api/users/{$user->id}");

        $response->assertStatus(200);

        $response->assertJson([
            "status" => "success",
            "message" => "User retrieved by ID",
            "data" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "role" => $user->role,
                "is_active" => $user->is_active,
            ]
        ]);
    }

    public function test_user_api_get_user_by_email(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->withoutMiddleware([
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ])
            ->get('/api/users/email?email=' . $user->email);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'email' => $user->email,
        ]);
    }

    public function test_user_api_verify_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this
            ->withoutMiddleware([
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ])
            ->get("/api/users/{$user->id}/verify");

        $response->assertStatus(200);

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
    }
}
