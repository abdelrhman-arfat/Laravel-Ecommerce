<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\JwtMiddleware;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Utils\Constants\ConstantEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductVariantsApiTest extends TestCase
{
    use RefreshDatabase;

    protected array $variantData;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a product to use in variant creation
        $product = Product::factory()->create();

        $this->variantData = [
            'product_id' => $product->id,
            'size'       => ConstantEnums::sizes()['M'],
            'color'      => ConstantEnums::colors()['red'],
            'quantity'   => 50,
            'is_active'  => true,
        ];
    }

    public function test_variant_api_can_create_variant()
    {
        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class
        ])->post('/api/variants', $this->variantData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $this->variantData['product_id'],
            'size'       => $this->variantData['size'],
            'color'      => $this->variantData['color'],
        ]);
    }

    public function test_variant_api_can_update_variant()
    {


        $variant = ProductVariant::factory()->create($this->variantData);

        $updateData = [
            'size'     => ConstantEnums::sizes()['L'],
            'color'    => ConstantEnums::colors()['blue'],
            'quantity' => 70,
        ];

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class
        ])->put("/api/variants/{$variant->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_variants', [
            'id'       => $variant->id,
            'size'     => $updateData['size'],
            'color'    => $updateData['color'],
            'quantity' => $updateData['quantity'],
        ]);
    }

    public function test_variant_api_can_soft_delete_variant()
    {


        $variant = ProductVariant::factory()->create($this->variantData);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class
        ])->delete("/api/variants/{$variant->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_variants', [
            'id'        => $variant->id,
            'is_active' => false,
        ]);
    }
    public function test_variant_api_restore_variant()
    {


        $variant = ProductVariant::factory()->create([
            "is_active" => false
        ]);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class
        ])->put("/api/variants/{$variant->id}/restore", $this->variantData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('product_variants', [
            'id'        => $variant->id,
            'is_active' => true,
        ]);
    }
    public function test_variant_api_cannot_create_duplicate_variant()
    {

        ProductVariant::factory()->create($this->variantData);

        $response = $this->withoutMiddleware([
            JwtMiddleware::class,
            AdminMiddleWare::class
        ])->post('/api/variants', $this->variantData);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'Variant already exists']);
    }
}
