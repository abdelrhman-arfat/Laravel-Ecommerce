<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_service_create_new_user(): void
    {
        $service = $this->app->make(UserService::class);

        $user = $service->create([
            'name' => 'Test User',
            'email' => 'test@example',
            'password' => "test123",
            'role' => 'user',
            'is_active' => true
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example', $user->email);
        $this->assertTrue(Hash::check('test123', $user->password));
    }
}
