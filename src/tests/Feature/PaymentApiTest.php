<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

namespace Tests\Feature;

use App\Helpers\BuildMetaData;
use App\Helpers\MerchantHelper;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Middleware\JwtMiddleware;
use App\Models\Order;
use App\Models\Payment;

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


        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data'
        ]);
        $this->assertTrue(filter_var($response['data'], FILTER_VALIDATE_URL) !== false);
    }
    public function test_payment_api_create_new_payment_url_with_paymob_fail_quantity(): void
    {
        $this->withoutMiddleware([JwtMiddleware::class]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            "quantity" => 1
        ]);


        $response = $this->post('/api/paymob', [
            'variants' => [
                [
                    'product_variant_id' => $variant1->id,
                    'quantity' => 3
                ],

            ]
        ]);

        $response->assertStatus(400);
    }
    public function test_payment_api_create_new_payment_url_with_paymob_fail_variant(): void
    {
        $this->withoutMiddleware([JwtMiddleware::class]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);



        $response = $this->post('/api/paymob', [
            'variants' => [
                [
                    'product_variant_id' =>  3,
                    'quantity' => 3
                ],

            ]
        ]);

        $response->assertStatus(400);
    }
    public function test_payment_api_create_new_payment_url_with_paymob_callback_success(): void
    {
        $this->withoutMiddleware([JwtMiddleware::class]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $v = ProductVariant::factory()->create([
            'product_id' => $product->id,
            "quantity" => 3
        ]);
        $v->requested_quantity = 2;
        $v->price = $product->price;
        $metadata = BuildMetaData::build($user, [$v], $product->price);
        $merchant_order_id = MerchantHelper::encoded($metadata);

        $response = $this->get('/api/paymob/callback?merchant_order_id=' . $merchant_order_id);

        $response->assertStatus(200);

        $order = Order::where("user_id", $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($order->status, "pending");
    }
}
