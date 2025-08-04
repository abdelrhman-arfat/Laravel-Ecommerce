<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderData;
    protected OrderService $orderService;
    protected array $statuses;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = $this->app->make(OrderService::class);
        $this->statuses = [
            "pending" => "pending",
            "completed" => "completed",
            "cancelled" => "cancelled",
        ];
    }

    public function test_order_service_create_new_order(): void
    {
        $payment = Payment::factory()->create();
        $user = User::factory()->create();

        $order = $this->orderService->create([
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'status' => $this->statuses["pending"],
            'total_price' => 100.45,

        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => $this->statuses["pending"],
        ]);
    }

    public function test_order_service_all(): void
    {
        Order::factory()->count(3)->create();
        $orders = $this->orderService->all();

        $this->assertCount(3, $orders);
    }

    public function test_order_service_find_user_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(4)->create(['user_id' => $user->id]);

        $orders = $this->orderService->findByUserId($user->id);

        $this->assertCount(4, $orders);
    }

    public function test_order_service_find_order_by_id(): void
    {
        $order = Order::factory()->create();
        $found = $this->orderService->find($order->id);

        $this->assertEquals($order->id, $found->id);
    }
    public function test_order_service_find_by_user_id_order_by_id(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            "user_id" => $user->id
        ]);
        $found = $this->orderService->findByUserIdAndOrderId($user->id, $order->id);

        $this->assertEquals($order->id, $found->id);
    }

    public function test_order_service_update_status(): void
    {
        $order = Order::factory()->create(['status' => $this->statuses['pending']]);
        $updated = $this->orderService->update($order, $this->statuses['completed']);

        $this->assertEquals($this->statuses['completed'], $updated->status);
    }

    public function test_order_service_cancel_order(): void
    {
        $order = Order::factory()->create(['status' => $this->statuses['pending']]);
        $cancelled = $this->orderService->cancel($order);

        $this->assertEquals($this->statuses['cancelled'], $cancelled->status);
    }

    public function test_order_service_restore_order(): void
    {
        $order = Order::factory()->create(['status' => $this->statuses['cancelled']]);
        $restored = $this->orderService->restore($order);

        $this->assertEquals($this->statuses['pending'], $restored->status);
    }

    public function test_order_service_search_by_status(): void
    {
        Order::factory()->create(['status' => $this->statuses['pending']]);
        Order::factory()->create(['status' => $this->statuses['completed']]);
        Order::factory()->create(['status' => $this->statuses['pending']]);

        $results = $this->orderService->searchByStatus($this->statuses['pending']);

        $this->assertTrue(
            $results->every(fn($order) => $order->status === $this->statuses['pending'])
        );
    }

    public function test_order_service_search_by_email(): void
    {
        $user1 = User::factory()->create(['email' => 'test1@example.com']);
        $user2 = User::factory()->create(['email' => 'other@example.com']);

        Order::factory()->create(['user_id' => $user1->id]);
        Order::factory()->create(['user_id' => $user1->id]);
        Order::factory()->create(['user_id' => $user2->id]);

        $results = $this->orderService->searchByEmail('test1@example.com');

        $this->assertCount(2, $results);
        foreach ($results as $order) {
            $this->assertEquals($user1->id, $order->user_id);
        }
    }
}
