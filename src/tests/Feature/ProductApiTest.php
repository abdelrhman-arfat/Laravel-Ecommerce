<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_product_api_all_products()
    {
        Product::factory()->count(10)->create();
        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get('/api/products');


        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['is_active', 'id', 'name', 'description', 'price']
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'total',
                    'per_page',
                ]
            ]);
    }

    public function test_product_api_trashed_active_products()
    {
        $activeProducts = Product::factory()->count(4)->create(['is_active' => true]);
        $trashedProducts = Product::factory()->count(4)->create(['is_active' => false]);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get("/api/products/trashed");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['is_active', 'id', 'name', 'description', 'price']
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'total',
                    'per_page',
                ]
            ]);
    }

    public function test_product_get_products_orders()
    {
        $product = Product::factory()->create();
        $productVariant = ProductVariant::factory()->create([
            "product_id" => $product->id
        ]);

        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            "order_id" => $order->id,
            "product_variant_id" => $productVariant->id
        ]);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get("/api/products/orders/{$product->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'total_price',
                    'created_at',
                    'updated_at',
                ]
            ],
            'message'
        ]);
    }

    public function test_product_api_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->get("/api/products/by-id/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ]
            ]);
    }


    public function test_product_api_create_product()
    {
        $variants = [
            [
                'size' => 'S',
                'color' => 'red',
                'quantity' => 10
            ],
            [
                'size' => 'M',
                'color' => 'blue',
                'quantity' => 20
            ],
            [
                'size' => 'L',
                'color' => 'green',
                'quantity' => 30
            ]
        ];
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            "variants" => $variants
        ];
        $response = $this->withoutMiddleware(
            [
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ]
        )->post('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Product'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100
        ]);

        foreach ($variants as $variant) {
            $this->assertDatabaseHas('product_variants', $variant);
        }
    }

    public function test_product_api_update_product()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 200,
        ];
        $response = $this->withoutMiddleware(
            [
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ]
        )->put("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_product_api_delete_product()
    {
        $product = Product::factory()->create();
        $response = $this->withoutMiddleware(
            [
                JwtMiddleware::class,
                AdminMiddleWare::class,
            ]
        )->delete("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }


    public function test_product_api_restore_product()
    {
        $product = Product::factory()->create(['is_active' => false]);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class,
        ])->put("/api/products/{$product->id}/restore");

        $response->assertStatus(200);
        $updatedProduct = $product->fresh();
        $this->assertTrue((bool) $updatedProduct->is_active);
    }
}
