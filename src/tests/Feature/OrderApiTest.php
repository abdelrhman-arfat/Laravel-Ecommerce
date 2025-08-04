<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
    }

    public function test_get_my_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withoutMiddleware([JwtMiddleware::class])
            ->actingAs($this->user)
            ->get('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [['id', 'user_id', 'status', 'total_price']],
                'message'
            ]);
    }

    public function test_get_single_order_for_user()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withoutMiddleware([JwtMiddleware::class])
            ->actingAs($this->user)
            ->get("/api/orders/by-id/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => ['id' => $order->id],
                'status' => true
            ]);
    }

    public function test_get_all_orders_as_admin()
    {
        Order::factory()->count(5)->create();

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get('/api/orders/admin');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'message'
            ]);
    }

    public function test_get_single_order_as_admin()
    {
        $order = Order::factory()->create();

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get("/api/orders/admin/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => ['id' => $order->id],
                'message' => 'Order retrieved successfully'
            ]);
    }

    public function test_update_order_status()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->put("/api/orders/admin/update-status", [
            'status' => 'completed',
            "order_id" => $order->id,
            'user_id' => $order->user_id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }



    public function test_admin_can_search_orders_by_email()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->getJson("/api/orders/admin/by-email?email=test@example.com");
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Orders retrieved successfully',
            ])
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_search_orders_by_status()
    {
        Order::factory()->count(3)->create(['status' => 'completed']);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->getJson("/api/orders/admin/by-status?status=completed");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Orders retrieved successfully',
            ])
            ->assertJsonStructure(['data']);
    }
}
