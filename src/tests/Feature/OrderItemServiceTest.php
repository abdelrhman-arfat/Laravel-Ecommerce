<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\OrderItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderItemService $orderItemService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemService = $this->app->make(OrderItemService::class);
    }

    public function test_order_item_service_create(): void
    {
        $order = Order::factory()->create();
        $variant = ProductVariant::factory()->create();

        $data = [
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
            'price' => 150.00
        ];

        $orderItem = $this->orderItemService->create($data);

        $this->assertDatabaseHas('order_items', [
            'id' => $orderItem->id,
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
            'price' => 150.00
        ]);
    }

    public function test_order_item_service_all(): void
    {
        OrderItem::factory()->count(5)->create();

        $items = $this->orderItemService->all();

        $this->assertCount(5, $items);
    }

    public function test_order_item_service_find(): void
    {
        $item = OrderItem::factory()->create();

        $found = $this->orderItemService->find($item->id);

        $this->assertEquals($item->id, $found->id);
    }

    public function test_order_item_service_update(): void
    {
        $item = OrderItem::factory()->create(['quantity' => 2]);

        $updated = $this->orderItemService->update($item, ['quantity' => 10]);

        $this->assertEquals(10, $updated->quantity);
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'quantity' => 10
        ]);
    }

    public function test_order_item_service_delete(): void
    {
        $item = OrderItem::factory()->create();

        $deleted = $this->orderItemService->delete($item);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
    }
}
