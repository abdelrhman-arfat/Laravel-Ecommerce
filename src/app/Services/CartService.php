<?php

namespace App\Services;

use App\Models\Cart;
use App\Services\Interfaces\CartInterface;

class CartService implements CartInterface
{
  public function find($userId, $cartId)
  {
    return Cart::where('id', $cartId)->where('user_id', $userId)->first();
  }

  public function create(array $data)
  {
    return Cart::create($data);
  }

  public function update(Cart $cart, array $data)
  {
    $cart->update($data);
    $cart->setRelation('product', null);
    $cart->setRelation('product_variant', null);

    return $cart;
  }

  public function delete($userId, $cartId)
  {
    return Cart::where('id', $cartId)->where('user_id', $userId)->delete();
  }
  public function getForUserByUserId($userId)
  {
    return Cart::with('productVariant.product')->where('user_id', $userId)->get();
  }

  public function getForUserByProductVariantIdAndUserId($userId, $productVariantId)
  {
    return Cart::where('user_id', $userId)->where('product_variant_id', $productVariantId)->first();
  }
}
