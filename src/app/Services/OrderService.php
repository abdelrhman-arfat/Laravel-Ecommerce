<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Interfaces\OrderInterface;

class OrderService implements OrderInterface
{
  public function all()
  {
    return Order::all();
  }
  public function find(int $id)
  {
    return Order::find($id);
  }
  public function findByUserId(int $userID)
  {
    return Order::where("user_id", $userID)->get();
  }
  public function create(array $data)
  {
    return Order::create($data);
  }
  public function update(Order $order, string $status)
  {
    $order->status = $status;
    $order->save();
    return $order;
  }
  public function cancel(Order $order)
  {
    $order->status = 'cancelled';
    $order->save();
    return $order;
  }
  public function restore(Order $order)
  {
    $order->status = 'pending';
    $order->save();
    return $order;
  }
  public function searchByStatus($status)
  {
    return Order::where('status', $status)->get();
  }
  public function searchByEmail($email)
  {
    return Order::whereHas('user', function ($query) use ($email) {
      $query->where('email', $email);
    })->get();
  }
}
