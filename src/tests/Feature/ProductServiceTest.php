<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $productService;
    protected $productData;
    protected $productUpdateData;
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->productService = $this->app->make(ProductService::class);

        $this->productUpdateData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 19.99,
            'is_active' => false,
        ];

        $this->productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.99,
            'is_active' => true,
        ];
    }
    /**
     * A basic feature test example.
     */
    public function test_product_service_create_new_product(): void
    {
        $product = $this->productService->create($this->productData);
        $this->assertEquals($product->name, $this->productData['name']);
        $this->assertEquals($product->description, $this->productData['description']);
        $this->assertEquals($product->price, $this->productData['price']);
        $this->assertEquals($product->is_active, $this->productData['is_active']);
    }
    public function test_product_service_update_product_success(): void
    {
        $product = Product::create($this->productData);
        $updatedProduct = $this->productService->update($product, $this->productUpdateData);
        $this->assertEquals($updatedProduct->name, $this->productUpdateData['name']);
        $this->assertEquals($updatedProduct->description, $this->productUpdateData['description']);
        $this->assertEquals($updatedProduct->price, $this->productUpdateData['price']);
        $this->assertEquals($updatedProduct->is_active, $this->productUpdateData['is_active']);
    }
    public function test_product_service_soft_delete_product(): void
    {
        $product = Product::create($this->productData);

        $deletedProduct = $this->productService->delete($product);
        $this->assertEquals($deletedProduct->is_active, false);
    }
    public function test_product_service_restore_product(): void
    {
        $product = Product::create([...$this->productData, 'is_active' => false]);
        $restoredProduct = $this->productService->restore($product);
        $this->assertEquals($restoredProduct->is_active, true);
    }
    public function test_product_service_trashed_products(): void
    {
        $products = Product::factory()->count(3)->state(['is_active' => false])->create();

        foreach ($products as $product) {
            ProductVariant::factory()->create(['product_id' => $product->id]);
        }
        $trashedProducts = $this->productService->trashed();

        $this->assertEquals(count($trashedProducts), 3);
        $this->assertInstanceOf(Product::class, $trashedProducts[0]);
        $this->assertTrue(
            $trashedProducts->every(fn($product) => !$product->is_active)
        );
        $this->assertTrue(
            $trashedProducts->every(fn($product) => $product->variants->count() > 0)
        );
    }
    public function test_product_service_all_products(): void
    {
        $products = Product::factory()->count(3)->state(['is_active' => true])->create();

        foreach ($products as $product) {
            ProductVariant::factory()->create(['product_id' => $product->id]);
        }
        $allProducts = $this->productService->all();

        $this->assertEquals($allProducts->count(), 3);
        $this->assertInstanceOf(Product::class, $allProducts[0]);
        // loop in all active products  
        $this->assertTrue(
            $allProducts->every(fn($product) => $product->is_active)
        );
        $this->assertTrue(
            $allProducts->every(fn($product) => $product->variants->count() > 0)
        );
    }
    public function test_product_service_all_products_with_trashed(): void
    {
        $products = Product::factory()->count(3)->create(['is_active' => false]);
        $activeProduct = Product::factory()->count(3)->create(['is_active' => true]);

        foreach ($products as $product) {
            ProductVariant::factory()->count(4)->create(['product_id' => $product->id]);
        }
        foreach ($activeProduct as $product) {
            ProductVariant::factory()->count(4)->create(['product_id' => $product->id]);
        }
        $allProducts = $this->productService->allWithTrashed();

        $this->assertEquals(count($allProducts), 6);
        $this->assertTrue(
            $allProducts->every(fn($product) => $product->variants->count() > 0)
        );
        $this->assertInstanceOf(Product::class, $allProducts[0]);
    }
    public function test_product_service_find_product(): void
    {
        $product = Product::factory()->create([...$this->productData, 'is_active' => true]);
        ProductVariant::factory()->create(['product_id' => $product->id]);
        $foundProduct = $this->productService->find($product->id);
        $this->assertEquals($foundProduct->id, $product->id);
    }
    public function test_product_service_find_by_names(): void
    {
        $product = Product::factory()->count(3)->create($this->productData);
        $foundProduct = $this->productService->search($this->productData['name']);
        $this->assertEquals(count($foundProduct), 3);
        $this->assertInstanceOf(Product::class, $foundProduct[0]);
        $this->assertTrue(
            $foundProduct->every(fn($product) => $product->name === $this->productData['name'])
        );
    }
}
