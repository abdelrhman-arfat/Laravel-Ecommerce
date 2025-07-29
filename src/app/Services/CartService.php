<?php

namespace App\Services;

use App\Models\Cart;
use App\Services\Interfaces\CartInterface;

class CartService implements CartInterface
{
  public function find($id)
  {
    return Cart::find($id);
  }

  public function create(array $data)
  {
    return Cart::create($data);
  }

  public function update(Cart $cart, array $data)
  {
    $cart->update($data);
    return $cart;
  }

  public function delete($id)
  {
    return Cart::where('id', $id)->delete(); 
  }
  public function getForUserByUserId($userId)
  {
    return Cart::where('user_id', $userId)->get();
  }

  public function getForUserByProductVariantIdAndUserId($userId, $productVariantId)
  {
    return Cart::where('user_id', $userId)->where('product_variant_id', $productVariantId)->first();
  }
}
