<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVariantRequest;
use App\Http\Requests\UpdateVariantRequest;
use App\Services\Interfaces\ProductVariantInterface;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    protected ProductVariantInterface $variantService;

    public function __construct(ProductVariantInterface $variantService)
    {
        $this->variantService = $variantService;
    }

    public function store(StoreVariantRequest $request)
    {
        try {
            $validated = $request->validated();
            $isDuplicate = $this->variantService->isDuplicate($validated);
            if ($isDuplicate) {
                return JsonResponseService::errorResponse(400, 'Variant already exists');
            }
            $variant = $this->variantService->create($validated);
            return JsonResponseService::successResponse($variant, 201, 'Variant created successfully');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    // Update existing variant
    public function update(UpdateVariantRequest $request, $id)
    {
        try {
            $variant = $this->variantService->find($id);
            if (!$variant) {
                return JsonResponseService::errorResponse(404, 'Variant not found');
            }

            $validated = $request->validated();

            $this->variantService->update($variant, $validated);
            return JsonResponseService::successResponse($variant, 200, 'Variant updated successfully');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $variant = $this->variantService->find($id);
            if (!$variant) {
                return JsonResponseService::errorResponse(404, 'Variant not found');
            }

            $this->variantService->delete($variant);
            return JsonResponseService::successResponse($variant, 200, 'Variant soft-deleted successfully');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function restore($id)
    {
        try {
            $variant = $this->variantService->find($id);
            if (!$variant) {
                return JsonResponseService::errorResponse(404, 'Variant not found');
            } else if ($variant->is_active) {
                return JsonResponseService::errorResponse(404, 'Variant is not soft-deleted');
            }

            $this->variantService->restore($variant);
            return JsonResponseService::successResponse($variant, 200, 'Variant restored successfully');
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
}
