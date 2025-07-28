<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    protected array $userData;
    protected $userService;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);

        $this->userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => "test123",
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ];
    }

    use RefreshDatabase;

    public function test_user_service_create_new_user(): void
    {

        $user = $this->userService->create($this->userData);


        // assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue(Hash::check('test123', $user->password));
    }

    public function test_user_service_update_user(): void
    {

        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // update the user
        $data = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => "updated123",
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ];

        // update the user
        $updatedUser = $this->userService->update($user, $data);

        // assert
        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals('updated@example.com', $updatedUser->email);
        $this->assertTrue(Hash::check('updated123', $updatedUser->password));
    }

    public function test_user_service_delete_user(): void
    {

        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // delete the user
        $deletedUser = $this->userService->delete($user);

        // assert
        $this->assertInstanceOf(User::class, $deletedUser);
        $this->assertFalse($deletedUser->is_active);
    }

    public function test_user_service_restore_user(): void
    {
        // create a user
        $user = User::create([
            ...$this->userData,
            'password' => Hash::make($this->userData['password']),
            'is_active' => false
        ]);

        // restore the user
        $restoredUser = $this->userService->restore($user);

        // assert
        $this->assertInstanceOf(User::class, $restoredUser);
        $this->assertTrue($restoredUser->is_active);
    }
    public function test_user_service_find_user(): void
    {
        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // find the user
        $foundUser = $this->userService->find($user->id);

        // assert
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->email, $foundUser->email);
    }

    public function test_user_service_find_user_by_email(): void
    {

        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // find the user by email
        $foundUser = $this->userService->findByEmail($user->email);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->email, $foundUser->email);
    }

    public function test_user_service_all_users(): void
    {

        // create some users
        User::factory()->count(5)->create();

        // get all users
        $users = $this->userService->all();

        // assert
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(5, $users);
    }
    public function test_user_service_get_user_order(): void
    {

        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // create an order
        $orders = Order::factory()->count(3)->create(['user_id' => $user->id]);

        // Get the user's orders from the service
        $userOrders = $this->userService->getUserOrder($user->id);

        // assert
        $this->assertCount(3, $userOrders);
        $this->assertInstanceOf(Order::class, $userOrders[0]);
        $this->assertEquals($orders->pluck('id')->sort()->values(), $userOrders->pluck('id')->sort()->values());
        $this->assertEquals($user->id, $userOrders[0]->user_id);
    }

    public function test_user_service_get_my_orders(): void
    {

        // create a user
        $user = User::create([...$this->userData, 'password' => Hash::make($this->userData['password'])]);

        // create an order
        $orders = Order::factory()->count(3)->create(['user_id' => $user->id]);

        // Get the user's orders from the service
        $userOrders = $this->userService->getMyOrders($user);

        // assert
        $this->assertCount(3, $userOrders);
        $this->assertInstanceOf(Order::class, $userOrders[0]);
        $this->assertEquals($orders->pluck('id')->sort()->values(), $userOrders->pluck('id')->sort()->values());
        $this->assertEquals($user->id, $userOrders[0]->user_id);
    }
}
