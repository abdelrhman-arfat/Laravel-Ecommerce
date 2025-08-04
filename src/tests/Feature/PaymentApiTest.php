<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Middleware\JwtMiddleware;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_api_create_new_payment_url_with_paymob_success(): void
    {
        $this->withoutMiddleware([JwtMiddleware::class]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->id
        ]);
        $variant2 = ProductVariant::factory()->create(
            [
                'product_id' => $product->id
            ]
        );

        $response = $this->post('/api/paymob', [
            'variants' => [
                [
                    'product_variant_id' => $variant1->id,
                    'quantity' => 3
                ],
                [
                    'product_variant_id' => $variant2->id,
                    'quantity' => 1
                ]
            ]
        ]);


        $response->assertJson([
            "message" => "P"
        ]);

    }
}
