<?php

namespace App\Services\Interfaces;

use App\Models\Order;

interface OrderInterface
{
  public function all();
  public function find(int $id);
  public function findByUserIdAndOrderId(int $userId, int $orderId);
  public function findByUserId(int  $id);
  public function create(array $data);
  public function update(Order $order, string $status);
  public function cancel(Order $order);
  public function restore(Order $order);
  public function searchByStatus($status);
  public function searchByEmail($email);
}
