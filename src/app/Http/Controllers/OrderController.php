<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\OrderInterface;
use App\Services\Interfaces\OrderItemInterface;
use App\Services\JsonResponseService;
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
        $user = $request->user();
        $orders = $this->orderService->findByUserId($user->id);
        return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
    }
    public function show($id)
    {
        $order = $this->orderService->find($id);
        return JsonResponseService::successResponse($order, 200, "Order retrieved successfully");
    }
    public function cancel($id)
    {
        $order = $this->orderService->find($id);
        if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
        $this->orderService->cancel($order);
        return JsonResponseService::successResponse(null, 200, "Order cancelled successfully");
    }
    public function restore($id)
    {
        $order = $this->orderService->find($id);
        if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
        $this->orderService->restore($order);
        return JsonResponseService::successResponse(null, 200, "Order restored successfully");
    }
    public function update(Request $request, $id)
    {
        $order = $this->orderService->find($id);
        if (!$order) return JsonResponseService::errorResponse(404, "Order not found");
        $this->orderService->update($order, $request->status);
        return JsonResponseService::successResponse(null, 200, "Order updated successfully");
    }
    public function search(Request $request)
    {
        $status = $request->status;
        $orders = $this->orderService->searchByStatus($status);
        return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
    }
    public function searchByEmail(Request $request)
    {
        $email = $request->email;
        $orders = $this->orderService->searchByEmail($email);
        return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
    }
    public function all()
    {
        $orders = $this->orderService->all();
        return JsonResponseService::successResponse($orders, 200, "Orders retrieved successfully");
    }
}
