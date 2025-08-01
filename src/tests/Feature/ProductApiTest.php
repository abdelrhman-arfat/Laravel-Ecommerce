<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use App\Models\Product;
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
                    '*' => ['id', 'name', 'description', 'price']
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
        ])->get("/api/products/{$product->id}");

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
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
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

        $this->assertDatabaseHas('products', $data);
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
