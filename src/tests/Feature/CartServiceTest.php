<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = $this->app->make(CartService::class);
    }

    public function test_cart_service_find()
    {
        $user  = User::factory()->create();
        $cart = Cart::factory()->create(
            [
                'user_id' => $user->id
            ]
        );

        $found = $this->cartService->find($user->id, $cart->id);

        $this->assertNotNull($found);
        $this->assertEquals($cart->id, $found->id);
    }

    public function test_cart_service_create()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $data = [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'price' => 14.99,
        ];

        $created = $this->cartService->create($data);

        $this->assertDatabaseHas('carts', $data);
        $this->assertEquals($data['user_id'], $created->user_id);
    }

    public function test_cart_service_update()
    {
        $cart = Cart::factory()->create(['quantity' => 1]);

        $updated = $this->cartService->update($cart, ['quantity' => 5]);

        $this->assertEquals(5, $updated->quantity);
        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'quantity' => 5]);
    }

    public function test_cart_service_delete()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);
        $deleted = $this->cartService->delete($user->id, $cart->id);

        $this->assertEquals(1, $deleted); // should return number of deleted rows
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }
    public function test_cart_service_get_for_user_by_user_id()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create();
        $variants = ProductVariant::factory()->count(3)->create([
            'product_id' => $product->id
        ]);

        foreach ($variants as $variant) {
            Cart::factory()->create([
                'user_id' => $user->id,
                'product_variant_id' => $variant->id,
            ]);
        }

        $result = $this->cartService->getForUserByUserId($user->id);

        $this->assertCount(3, $result);

        foreach ($result as $cart) {
            $this->assertNotNull($cart->productVariant);
            $this->assertNotNull($cart->productVariant->product);
        }
    }


    public function test_cart_service_get_for_user_by_product_variant_id_and_user_id()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
        ]);

        $result = $this->cartService->getForUserByProductVariantIdAndUserId($user->id, $variant->id);

        $this->assertNotNull($result);
        $this->assertEquals($cart->id, $result->id);
    }
}
