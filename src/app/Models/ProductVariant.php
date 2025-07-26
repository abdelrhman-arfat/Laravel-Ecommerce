<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    //
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'color', 'size', 'quantity'];
    protected $hidden = ['created_at', 'updated_at'];

    protected function product()
    {
        return $this->belongsTo(Product::class);
    }
}
