<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserOrderItem extends Model
{
    protected $table = 'user_order_items';
    protected $fillable = ['uuid','order_id','seller_id','item_type','item_id','quantity','price'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $m->uuid = (string) Str::uuid();
        });
    }
    public function getItemModelAttribute()
    {
        if ($this->item_type === 'user_products') {
            return UserProduct::find($this->item_id);
        }
        if ($this->item_type === 'user_services') {
            return UserService::find($this->item_id);
        }
        return null;
    }
}
