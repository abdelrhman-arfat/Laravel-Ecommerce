<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Services\Interfaces\OrderItemInterface;

class OrderItemService implements OrderItemInterface
{
  public function all()
  {
    return OrderItem::with(['order', 'productVariant'])->get();
  }

  public function find(int $id)
  {
    return OrderItem::with(['order', 'productVariant'])->findOrFail($id);
  }

  public function create(array $data)
  {
    return OrderItem::create($data);
  }

  public function update(OrderItem $orderItem, array $data)
  {
    $orderItem->update($data);
    return $orderItem;
  }

  public function delete(OrderItem $orderItem)
  {
    return $orderItem->delete();
  }
}
