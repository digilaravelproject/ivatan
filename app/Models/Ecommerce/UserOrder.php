<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserOrder extends Model
{
    protected $table = 'user_orders';

    protected $fillable = [
        'uuid',
        'buyer_id',
        'total_amount',
        'status',
        'payment_status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Relationships

    public function items()
    {
        return $this->hasMany(UserOrderItem::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(UserPayment::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(UserPayment::class, 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(UserShipping::class, 'order_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class, 'order_id');
    }
}
