<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Services\Interfaces\OrderItemInterface;

class OrderItemService implements OrderItemInterface
{
  public function all(int $orderId)
  {
    return OrderItem::where('order_id', $orderId)->get();
  }

  public function find($id)
  {
    return OrderItem::find($id);
  }
  public function create(array $data)
  {
    return OrderItem::create($data);
  }
  public function update(OrderItem $orderIem, array $data)
  {
    $orderIem->update($data);
    return $orderIem;
  }
  public function delete(OrderItem $orderItem)
  {
    $orderItem->is_active = false;
    $orderItem->save();
    return $orderItem;
  }
  public function restore(OrderItem $orderItem)
  {
    $orderItem->is_active = true;
    $orderItem->save();
    return $orderItem;
  }
  public function getByProductVariantId(int $productVariantId)
  {
    return OrderItem::where('product_variant_id', $productVariantId)->get();
  }
}
