<?php

namespace App\Helpers;

class MerchantHelper
{
  public static function encoded($metadata)
  {
    return base64_encode(json_encode($metadata)) . '.' . uniqid();
  }
  public static function decoded($merchant_order_id)
  {
    $metadata = json_decode(base64_decode($merchant_order_id), true);
    return $metadata;
  }
}
