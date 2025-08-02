<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\CacheService;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductInterface;
use App\Services\Interfaces\ProductVariantInterface;
use App\Services\JsonResponseService;

class ProductController extends Controller
{
    protected ProductInterface $productService;
    protected ProductVariantInterface $productVariantService;

    public function __construct(ProductInterface $productService, ProductVariantInterface $productVariantService)
    {
        $this->productService = $productService;
        $this->productVariantService = $productVariantService;
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);

            $key = CacheService::key('products_active', ['limit' => $limit, "page" => $request->query('page', 1)]);

            $products = CacheService::remember($key, 15, function () use ($limit) {
                return $this->productService->all($limit);
            });

            return JsonResponseService::successResponseForPagination($products, 200, 'Trashed products retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function trashed(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $key = CacheService::key('products_trashed', ['limit' => $limit, "page" => $request->query('page', 1)]);

            $products = CacheService::remember($key, 15, function () use ($limit) {
                return $this->productService->trashed($limit);
            });

            return JsonResponseService::successResponseForPagination($products, 200, 'Trashed products retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function allWithTrashed(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $key = CacheService::key('products_all', ['limit' => $limit, "page" => $request->query('page', 1)]);

            $products = CacheService::remember($key, 15, function () use ($limit) {
                return $this->productService->allWithTrashed($limit);
            });

            return JsonResponseService::successResponseForPagination($products, 200, 'Trashed products retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }


    public function show($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            }
            return JsonResponseService::successResponse($product, 200, 'Product retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function store(CreateProductRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $product = $this->productService->create([
                'name'        => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'price'       => $validatedData['price'],
            ]);

            foreach ($validatedData['variants'] as $variantData) {
                $variantData['product_id'] = $product->id;
                $this->productVariantService->create($variantData);
            }


            return JsonResponseService::successResponse($product, 201, 'Product created');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            }

            $updated = $this->productService->update($product, $request->validated());
            return JsonResponseService::successResponse($updated, 200, 'Product updated');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            }

            $deleted = $this->productService->delete($product);
            return JsonResponseService::successResponse($deleted, 200, 'Product soft-deleted');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            } else if ($product->is_active) {
                return JsonResponseService::errorResponse(404, 'Product is not soft-deleted');
            }

            $restored = $this->productService->restore($product);
            return JsonResponseService::successResponse($restored, 200, 'Product restored');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $name = $request->query('name');
            $results = $this->productService->search($name);
            return JsonResponseService::successResponse($results, 200, "Search results for: $name");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function variants($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            }

            $variants = $this->productService->getVariants($product);
            return JsonResponseService::successResponse($variants, 200, 'Product variants retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function orders($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return JsonResponseService::errorResponse(404, 'Product not found');
            }

            $orders = $this->productService->getOrders($product);
            return JsonResponseService::successResponse($orders, 200, 'Product orders retrieved');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
}
