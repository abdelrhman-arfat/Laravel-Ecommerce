<?php

namespace App\Services\Interfaces;

use App\Models\Product;

interface ProductInterface
{
  public function all();
  public function find($id);
  public function create(array $data);
  public function update(Product $product, array $data);
  public function delete(Product $product);
  public function restore(Product $product);
  public function search($name);
  public function getVariants($product);
  
}
