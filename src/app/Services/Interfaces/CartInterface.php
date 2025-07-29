<?php


namespace App\Services\Interfaces;

use App\Models\Cart;

interface CartInterface
{
  public function find($id);
  public function create(array $data);
  public function update(Cart $cart, array $data);
  public function delete($id);
  public function getForUserByUserId($userId);
  public function getForUserByProductVariantIdAndUserId($userId, $productVariantId);
}
