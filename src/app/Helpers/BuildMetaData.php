<?php

namespace App\Helpers;

class BuildMetaData
{
  public static function build($user, $variants, $totalPrice)
  {
    [
      'user_id' => $user->id,
      'variants' => array_map(function ($variant) {
        return [
          'product_variant_id' => $variant->id,
          'quantity' => $variant->requested_quantity,
          'price' => $variant->price,
        ];
      }, $variants),
      'total_price' => $totalPrice,
      'time' => now()
    ];
  }
}
