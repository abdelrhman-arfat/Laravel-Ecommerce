<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['id', 'name', 'description', 'price'];
    protected $hidden = ['created_at', 'updated_at'];


    protected function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
