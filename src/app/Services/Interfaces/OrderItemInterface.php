<?php

namespace App\Services\Interfaces;

use App\Models\OrderItem;

interface OrderItemInterface
{

  public function all(int $orderId);
  public function find($id);
  public function create(array $data);
  public function update(OrderItem $orderIem, array $data);
  public function delete(OrderItem $orderItem);
  public function restore(OrderItem $orderItem);
  public function getByProductVariantId(int $productVariantId);
}
