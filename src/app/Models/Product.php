<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = ['id', 'name', 'description', 'price', 'is_active'];
    protected $hidden = ['created_at', 'updated_at'];


    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }


    /**
     * ProductVariant has product_id
     * OrderItem has product_variant_id
     * OrderItem has order_id
     * ProductVariant with this id
     * then get the order_item with this variant id
     * then get the order who the order_items has order_id
     */
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            OrderItem::class,
            'product_variant_id',
            'id',
            'id',
            'order_id'
        )->whereIn('product_variant_id', function ($query) {
            $query->select('id')
                ->from('product_variants')
                ->where('product_variants.product_id', $this->id);
        });
    }
}
