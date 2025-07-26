<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['payment_session'];

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
