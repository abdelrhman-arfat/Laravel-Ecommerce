<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchOrderByEmailRequest;
use App\Http\Requests\SearchOrderByStatusRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\CacheService;
use App\Services\Interfaces\OrderInterface;
use App\Services\Interfaces\OrderItemInterface;
use App\Services\JsonResponseService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderItemService;
    protected $orderService;
    public function __construct(OrderInterface $orderService, OrderItemInterface $orderItemService)
    {
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
    }
    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $page = $request->query('page', 1);
            $key = CacheService::key('orders', ['limit' => $limit, "page" => $page]);

            $orders = CacheService::remember($key, 15, function () use ($limit) {
                return  $this->orderService->all();
            });
            return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = $this->orderService->findByUserIdAndOrderId($user->id, $id);
            if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
            return JsonResponseService::successResponse($order, 200, "Order retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function showFromAdmin($id)
    {
        try {
            $order = $this->orderService->find($id);
            if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
            return JsonResponseService::successResponse($order, 200, "Order retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function cancel(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = $this->orderService->findByUserIdAndOrderId($user->id, $id);
            if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
            $this->orderService->cancel($order);
            return JsonResponseService::successResponse(null, 200, "Order cancelled successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function restore(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = $this->orderService->findByUserIdAndOrderId($user->id, $id);
            if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
            $this->orderService->restore($order);
            return JsonResponseService::successResponse(null, 200, "Order restored successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function update(UpdateOrderStatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $userId = $validated['user_id'];
            $status = $validated['status'];
            $o =  $request->getOrder();

            if ($o->user_id != $userId) return JsonResponseService::errorResponse(404, "Order not found");
            if ($o->status == $status) return JsonResponseService::errorResponse(404, "Order already has this status");
            
            $order = $this->orderService->update($o, $status);

            $order = $this->orderService->update($order, $request->status);

            return JsonResponseService::successResponse($order, 200, "Order updated successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function getMyOrders(Request $request)
    {
        try {
            $user = $request->user();
            $orders = $this->orderService->findByUserId($user->id);
            return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }

    public function searchByStatus(SearchOrderByStatusRequest $request)
    {
        try {
            $status = $request->status;
            $key = CacheService::key('orders_by_status', ['status' => $status]);

            $orders = CacheService::remember($key, 15, function () use ($status) {
                return $this->orderService->searchByStatus($status);
            });
            return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
    public function searchByEmail(SearchOrderByEmailRequest $request)
    {
        try {
            $email = $request->email;

            $key = CacheService::key('orders_by_email', ['email' => $email]);

            $orders = CacheService::remember($key, 15, function () use ($email) {
                return  $this->orderService->searchByEmail($email);
            });
            return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
        } catch (Exception $e) {
            return JsonResponseService::errorResponse(500, $e->getMessage());
        }
    }
}
