<?php


namespace App\Services\Interfaces;

use App\Models\Cart;

interface CartInterface
{
  public function find($userId, $orderId);
  public function create(array $data);
  public function update(Cart $cart, array $data);
  public function delete($userId, $cartId);
  public function getForUserByUserId($userId);
  public function getForUserByProductVariantIdAndUserId($userId, $productVariantId);
}
