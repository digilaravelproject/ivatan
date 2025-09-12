<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;

class UserCartItem extends Model
{
    protected $fillable = [
        'uuid',
        'cart_id',
        'seller_id',
        'item_type',
        'item_id',
        'price',
        'quantity'
    ];

    public function cart()
    {
        return $this->belongsTo(UserCart::class, 'cart_id');
    }

    public function item()
    {
        return $this->morphTo();
    }
    // public function product()
    // {
    //     return $this->belongsTo(UserProduct::class);
    // }
    public function product()
    {
        return $this->morphTo(UserProduct::class, 'item_type', 'item_id');
    }
}
