<?php

namespace Tests\Feature;

use App\Http\Middleware\JwtMiddleware;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartApiTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_cart_api_get_all_carts(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id
        ]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
        ]);

        $response = $this->get('/api/carts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'price',
                    'quantity',
                    'product_variant' => [
                        'id',
                        'product_id',
                        'color',
                        'size',
                        'quantity',
                        'is_active',
                        'product' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'is_active',
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function test_cart_api_create_cart(): void
    {


        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $productVariant = ProductVariant::factory()->create([
            'quantity' => 10,
        ]);

        $payload = [
            'product_variant_id' => $productVariant->product_id,
            'quantity' => 2,
        ];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'user_id',
                'price',
                'quantity',
            ]
        ]);
    }

    public function test_cart_api_create_cart_with_invalid_product_variant_id(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'product_variant_id' => 99999,
            'quantity' => 2,
        ];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(400);
    }

    public function test_cart_api_create_cart_with_exceeding_quantity(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $productVariant = ProductVariant::factory()->create(['quantity' => 5]);

        $payload = [
            'product_variant_id' => $productVariant->product_id,
            'quantity' => 10,
        ];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'Quantity is greater than available quantity'
        ]);
    }

    public function test_cart_api_create_duplicate_cart(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $productVariant = ProductVariant::factory()->create(['quantity' => 5]);

        // أول مرة
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $productVariant->id
        ]);

        $payload = [
            'product_variant_id' => $productVariant->product_id,
            'quantity' => 1,
        ];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'Cart already exists'
        ]);
    }

    public function test_cart_api_create_cart_with_invalid_quantity(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $productVariant = ProductVariant::factory()->create();

        $payload = [
            'product_variant_id' => $productVariant->product_id,
            'quantity' => 0,
        ];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            "message",
        ]);
    }

    public function test_cart_api_create_cart_with_missing_fields(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [];

        $response = $this->post('/api/carts', $payload);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            "message",
        ]);
    }
    public function test_cart_api_delete_success(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);


        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->delete('/api/carts/' . $cart->id);

        $response->assertStatus(200);
    }
    public function test_cart_api_delete_not_found_not_owner(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);


        $cartOwner = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $cartOwner->id
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete('/api/carts/' . $cart->id);
        $response->assertStatus(404);
    }
    public function test_cart_api_delete_not_found_not_exists(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete('/api/carts/' . 1);
        $response->assertStatus(404);
    }

    public function test_cart_api_update_success(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['quantity' => 10, 'product_id' => $product->id]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 5,
        ];

        $response = $this->put('/api/carts/' . $cart->id, $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $cart->id,
                'user_id' => $user->id,
                'product_variant_id' => $variant->id,
                'quantity' => $payload['quantity'],
                'price' => $product->price * $payload['quantity'],
            ],

        ]);
    }

    public function test_cart_api_update_not_found(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'quantity' => 1,
        ];

        $response = $this->put('/api/carts/999', $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'The selected cart is invalid.',
        ]);
    }
    public function test_cart_api_update_not_owned_by_user(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        $owner = User::factory()->create();
        /** @var \App\Models\User $otherUser */
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $variant = ProductVariant::factory()->create(['quantity' => 10]);

        $cart = Cart::factory()->create([
            'user_id' => $owner->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 2,
        ];

        $response = $this->put('/api/carts/' . $cart->id, $payload);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Cart not found',
        ]);
    }
    public function test_cart_api_update_quantity_greater_than_available(): void
    {
        $this->withoutMiddleware([
            JwtMiddleware::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $variant = ProductVariant::factory()->create(['quantity' => 5]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 10,
        ];

        $response = $this->put('/api/carts/' . $cart->id, $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Quantity is greater than available quantity',
        ]);
    }
}
