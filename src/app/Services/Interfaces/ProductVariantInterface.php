<?php

namespace App\Services\Interfaces;

use App\Models\ProductVariant;

interface ProductVariantInterface
{
  public function all(int $productId);
  public function find(int $id);
  public function create(array $data);
  public function update(ProductVariant $product, array $data);
  public function restore(ProductVariant $product);
  public function delete(ProductVariant $product);
  public function decreaseQuantity(ProductVariant $product, $quantity);
}
