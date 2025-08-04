<?php

namespace App\Http\Controllers;

use App\Helpers\BuildMetaData;
use App\Helpers\MerchantHelper;
use App\Http\Requests\CreatePaymentRequest;
use App\Services\Interfaces\OrderInterface;
use App\Services\Interfaces\OrderItemInterface;
use App\Services\Interfaces\PaymentInterface;
use App\Services\Interfaces\ProductVariantInterface;
use App\Services\JsonResponseService;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymobService;
    protected $paymentService;
    protected $orderService;
    protected $orderItemService;
    protected $variantService;


    public function __construct(ProductVariantInterface $variantService, PaymobService $paymobService, PaymentInterface $paymentService, OrderInterface $orderService, OrderItemInterface $orderItemService)
    {
        $this->paymentService = $paymentService;
        $this->paymobService = $paymobService;
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
        $this->variantService = $variantService;
    }
    public function store(CreatePaymentRequest $request)
    {
        try {

            $variants = $request->getVariants();
            $user = $request->user();

            $totalPrice = 0;
            foreach ($variants as $variant) {
                $totalPrice += $variant->product->price * $variant->requested_quantity;
            }

            if ($totalPrice <= 0) return JsonResponseService::errorResponse(400, "Total price must be greater than 0");

            $metadata = BuildMetaData::build($user, $variants, $totalPrice);

            $paymentUrl = $this->paymobService->getPaymentUrl(
                $totalPrice,
                $user->email,
                $user->name,
                $metadata
            );
            return JsonResponseService::successResponse($paymentUrl, 201, "Order created successfully");
        } catch (\Exception $e) {
            return JsonResponseService::errorResponse(400, $e->getMessage());
        }
    }
    public function callback(Request $request)
    {
        $data = $request->all();

        $rawEncodedMetadata = $data['merchant_order_id'] ?? null;
        if (!$rawEncodedMetadata) return JsonResponseService::errorResponse(400, "Invalid merchant_order_id format");

        $merchant_order_id = explode('.', $rawEncodedMetadata)[0] ?? null;
        if (!$merchant_order_id) return JsonResponseService::errorResponse(400, "Invalid merchant_order_id format");

        $metadata = MerchantHelper::decoded($merchant_order_id);
        if (!$metadata || !isset($metadata['user_id'], $metadata['variants']) || !isset($metadata['total_price'])) {
            return JsonResponseService::errorResponse(400, "Invalid merchant_order_id format");
        }

        $user = $request->user();
        if ($user->id != $metadata['user_id']) {
            return JsonResponseService::errorResponse(400, "You are not authorized to create this order");
        }

        // Avoid duplicate payment creation
        $isPayment = $this->paymentService->find($merchant_order_id);
        if ($isPayment) {
            return JsonResponseService::errorResponse(400, "Payment already created");
        }

        try {
            DB::beginTransaction();

            // Create payment
            $payment = $this->paymentService->create($merchant_order_id);

            // Create order
            $order = $this->orderService->create([
                "user_id" => $metadata['user_id'],
                "status" => "pending",
                'payment_id' => $payment->id,
                "total_price" => $metadata['total_price']
            ]);

            // Create order items and update variant stock
            foreach ($metadata['variants'] as $v) {
                $variant = $this->variantService->find($v['product_variant_id']);
                $this->variantService->decreaseQuantity($variant, $v['quantity']);

                $this->orderItemService->create([
                    "order_id" => $order->id,
                    "product_variant_id" => $variant->id,
                    "quantity" => $v['quantity'],
                    "price" => $v['price']
                ]);
            }

            DB::commit();
            return JsonResponseService::successResponse($order, 200, "Order created successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            return JsonResponseService::errorResponse(500, "Something went wrong during order processing");
        }
    }
}
