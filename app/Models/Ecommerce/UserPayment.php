<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserPayment extends Model
{
    protected $table = 'user_payments';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const GATEWAY_COD = 'cod';
    const GATEWAY_RAZORPAY = 'razorpay';
    const GATEWAY_STRIPE = 'stripe';

    protected $fillable = ['uuid', 'order_id', 'gateway', 'amount', 'status', 'transaction_id', 'meta'];
    protected $casts = ['meta' => 'array', 'amount' => 'decimal:2'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $m->uuid = (string) Str::uuid();
        });
    }
    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'order_id');
    }
}
