<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Services\Interfaces\CartInterface;
use App\Services\Interfaces\ProductVariantInterface;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    protected $productVariantService;
    public function __construct(CartInterface $cartService, ProductVariantInterface $productVariantService)
    {
        $this->cartService = $cartService;
        $this->productVariantService = $productVariantService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $carts = $this->cartService->getForUserByUserId($user->id);
        return JsonResponseService::successResponse($carts, 200, "Cart retrieved successfully");
    }

    public function store(StoreCartRequest $request)
    {
        $data = $request->validated();

        $productVariant = $request->getProductVariant();

        if ($data["quantity"] > $productVariant->quantity) {
            return JsonResponseService::errorResponse(400, "Quantity is greater than available quantity");
        }

        $isCartExists = $this->cartService->getForUserByProductVariantIdAndUserId($request->user()->id, $productVariant->id);

        if ($isCartExists) {
            return JsonResponseService::errorResponse(400, "Cart already exists");
        }

        $data["product_variant_id"] = $productVariant->id;
        $data["price"]              = $productVariant->price * $data["quantity"];
        $data["user_id"]            = $request->user()->id;

        $cart = $this->cartService->create($data);
        $cart->makeHidden(['product', 'product_variant']);
        return JsonResponseService::successResponse($cart, 201, "Cart created successfully");
    }
    public function destroy(Request $request, $id)
    {
        if (!$id) return JsonResponseService::errorResponse(404, "Cart not found");
        $user = $request->user();
        $isExists = $this->cartService->find($user->id, $id);
        if (!$isExists) {
            return JsonResponseService::errorResponse(404, "Cart not found");
        }
        $this->cartService->delete($user->id, $id);
        return JsonResponseService::successResponse(null, 200, "Cart deleted successfully");
    }

    public function update(UpdateCartRequest $request, $id)
    {
        $data = $request->validated();
        $user = $request->user();

        $cart = $request->getCart();

        $product = $cart->product;

        if ($user->id !== $cart->user_id) {
            return JsonResponseService::errorResponse(404, "Cart not found");
        }

        if ($data["quantity"] > $cart->productVariant->quantity) {
            return JsonResponseService::errorResponse(400, "Quantity is greater than available quantity");
        }

        if (isset($data["quantity"]) && $data["quantity"] !== $cart->quantity) {
            $data["price"] = $product->price * $data["quantity"];
        }
        $cart = $this->cartService->update($cart, $data);
        return JsonResponseService::successResponse($cart, 200, "Cart updated successfully");
    }
}
