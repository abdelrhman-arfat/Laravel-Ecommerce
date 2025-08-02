<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Services\Interfaces\ProductVariantInterface;

class ProductVariantService implements ProductVariantInterface
{
  public function all(int $productId)
  {
    return ProductVariant::where('product_id', $productId)->get();
  }
  public function find(int $id)
  {
    return ProductVariant::find($id);
  }
  public function create(array $data)
  {
    return ProductVariant::create($data);
  }
  public function update(ProductVariant $product, array $data)
  {
    return $product->update($data);
  }
  public function delete(ProductVariant $product)
  {
    $product->is_active = false;
    $product->save();
    return $product;
  }
  public function isDuplicate(array $data)
  {
    return ProductVariant::where('product_id', $data['product_id'])
      ->where('color', $data['color'])
      ->where('size', $data['size'])
      ->exists();
  }
  public function restore(ProductVariant $product)
  {
    $product->is_active = true;
    $product->save();
    return $product;
  }
  public function decreaseQuantity(ProductVariant $product, $quantity)
  {
    $product->quantity -= $quantity;
    $product->save();
    return $product;
  }
}
