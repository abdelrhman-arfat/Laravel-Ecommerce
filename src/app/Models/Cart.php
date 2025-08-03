<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'product_variant_id',
        'quantity',
        'price',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }


    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            "product_id",
            "id",
            "product_variant_id",
            "id",
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
