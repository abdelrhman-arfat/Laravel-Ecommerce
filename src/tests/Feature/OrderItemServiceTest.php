<?php

namespace Tests\Feature;

use App\Models\OrderItem;
use App\Models\Order;
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

    public function test_order_item_service_all_returns_order_items_by_order_id()
    {
        $order = Order::factory()->create();
        $items = OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

        $result = $this->orderItemService->all($order->id);

        $this->assertCount(3, $result);
        $this->assertEquals($items->pluck('id')->toArray(), $result->pluck('id')->toArray());
    }

    public function test_order_item_service_find_returns_specific_order_item()
    {
        $item = OrderItem::factory()->create();

        $found = $this->orderItemService->find($item->id);

        $this->assertNotNull($found);
        $this->assertEquals($item->id, $found->id);
    }

    public function test_order_item_service_create_creates_new_order_item()
    {
        $order = Order::factory()->create();
        $variant = ProductVariant::factory()->create();

        $data = [
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'price' => 100.0,
            'is_active' => true,
        ];

        $created = $this->orderItemService->create($data);

        $this->assertDatabaseHas('order_items', $data);
        $this->assertEquals($data['order_id'], $created->order_id);
    }

    public function test_order_item_service_update_updates_existing_order_item()
    {
        $item = OrderItem::factory()->create(['quantity' => 1]);

        $updated = $this->orderItemService->update($item, ['quantity' => 5]);

        $this->assertEquals(5, $updated->quantity);
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'quantity' => 5]);
    }

    public function test_order_item_service_delete_sets_is_active_false()
    {
        $item = OrderItem::factory()->create(['is_active' => true]);

        $deleted = $this->orderItemService->delete($item);

        $this->assertFalse($deleted->is_active);
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'is_active' => false]);
    }

    public function test_order_item_service_restore_sets_is_active_true()
    {
        $item = OrderItem::factory()->create(['is_active' => false]);

        $restored = $this->orderItemService->restore($item);

        $this->assertTrue($restored->is_active);
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'is_active' => true]);
    }

    public function test_order_item_service_get_by_product_variant_id()
    {
        $variant = ProductVariant::factory()->create();
        $items = OrderItem::factory()->count(2)->create(['product_variant_id' => $variant->id]);

        $result = $this->orderItemService->getByProductVariantId($variant->id);

        $this->assertCount(2, $result);
        $this->assertEquals($items->pluck('id')->toArray(), $result->pluck('id')->toArray());
    }
}
