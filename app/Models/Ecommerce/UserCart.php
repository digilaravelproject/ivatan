<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
  protected $fillable = ['uuid', 'user_id'];

    public function items()
    {
        return $this->hasMany(UserCartItem::class, 'cart_id');
    }
}
