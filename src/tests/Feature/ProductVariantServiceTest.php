<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductVariantService;
use App\Utils\Constants\ConstantEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductVariantService $productVariantService;
    protected array $productVariantData;
    protected array $productVariantUpdateData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productVariantService = $this->app->make(ProductVariantService::class);

        $product = Product::factory()->create();

        $this->productVariantData = [
            'product_id' => $product->id,
            'size' => ConstantEnums::sizes()['M'],
            'color' => ConstantEnums::colors()['red'],
            'quantity' => 100,
            'is_active' => true,
        ];

        $this->productVariantUpdateData = [
            'size' => ConstantEnums::sizes()['L'],
            'color' => ConstantEnums::colors()['blue'],
            'quantity' => 80,
        ];
    }

    public function test_product_variant_service_create_product_variant(): void
    {
        $variant = $this->productVariantService->create($this->productVariantData);

        $this->assertInstanceOf(ProductVariant::class, $variant);
        $this->assertEquals($variant->size, ConstantEnums::sizes()['M']);
        $this->assertEquals($variant->color, ConstantEnums::colors()['red']);
    }

    public function test_product_variant_service_update_product_variant(): void
    {
        $variant = ProductVariant::factory()->create($this->productVariantData);

        $this->productVariantService->update($variant, $this->productVariantUpdateData);
        $variant->refresh();

        $this->assertEquals($variant->size, ConstantEnums::sizes()['L']);
        $this->assertEquals($variant->color, ConstantEnums::colors()['blue']);
        $this->assertEquals($variant->quantity, 80);
    }

    public function test_product_variant_service_soft_delete_product_variant(): void
    {
        $variant = ProductVariant::factory()->create($this->productVariantData);

        $deleted = $this->productVariantService->delete($variant);
        $this->assertFalse($deleted->is_active);
    }

    public function test_product_variant_service_restore_product_variant(): void
    {
        $variant = ProductVariant::factory()->create([
            ...$this->productVariantData,
            'is_active' => false,
        ]);

        $restored = $this->productVariantService->restore($variant);
        $this->assertTrue($restored->is_active);
    }

    public function test_product_variant_service_decrease_quantity(): void
    {
        $variant = ProductVariant::factory()->create([
            ...$this->productVariantData,
            'quantity' => 100,
        ]);

        $updated = $this->productVariantService->decreaseQuantity($variant, 20);
        $this->assertEquals($updated->quantity, 80);
    }

    public function test_product_variant_service_find_product_variant(): void
    {
        $variant = ProductVariant::factory()->create($this->productVariantData);

        $found = $this->productVariantService->find($variant->id);
        $this->assertEquals($found->id, $variant->id);
    }

    public function test_product_variant_service_get_all_variants_for_product(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);

        $variants = $this->productVariantService->all($product->id);

        $this->assertCount(3, $variants);
        $this->assertInstanceOf(ProductVariant::class, $variants[0]);
    }

    public function test_product_variant_service_check_if_variant_exists(): void
    {
        $isDuplicate = $this->productVariantService->isDuplicate($this->productVariantData);
        $this->assertFalse($isDuplicate);

        ProductVariant::factory()->create($this->productVariantData);

        $isDuplicate = $this->productVariantService->isDuplicate($this->productVariantData);
        $this->assertTrue($isDuplicate);

        $isDuplicate = $this->productVariantService->isDuplicate([
            ...$this->productVariantData,
            'color' => ConstantEnums::colors()['blue'],
        ]);
        $this->assertFalse($isDuplicate);

        $isDuplicate = $this->productVariantService->isDuplicate([
            ...$this->productVariantData,
            'size' => ConstantEnums::sizes()['L'],
        ]);
        $this->assertFalse($isDuplicate);

        $isDuplicate = $this->productVariantService->isDuplicate([
            ...$this->productVariantData,
            'size' => ConstantEnums::sizes()['L'],
            'color' => ConstantEnums::colors()['blue'],
        ]);
        $this->assertFalse($isDuplicate);
    }
}
