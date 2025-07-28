<?php

namespace App\Services;

use App\Models\Product;
use App\Services\Interfaces\ProductInterface;

class ProductService implements ProductInterface
{

  public function all()
  {
    return Product::where('is_active', true)->get();
  }
  public function trashed()
  {
    return Product::where('is_active', false)->get();
  }
  public function allWithTrashed()
  {
    return Product::all();
  }
  public function find($id)
  {
    return Product::find($id);
  }
  public function create(array $data)
  {
    return Product::create($data);
  }
  public function update(Product $product, array $data)
  {
    $product->update($data);
    $product->fresh();
    return $product;
  }
  public function delete(Product $product)
  {
    $product->is_active = false;
    $product->save();
    return $product;
  }
  public function restore(Product $product)
  {
    $product->is_active = true;
    $product->save();
    return $product;
  }
  public function search($name)
  {
    return Product::where('name', 'like', '%' . $name . '%')->get();
  }
  public function getVariants($product)
  {
    return $product->variants;
  }
  public function getOrders($product)
  {
    return $product->orders;
  }
}
