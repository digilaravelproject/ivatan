<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property string $gateway
 * @property numeric $amount
 * @property string $status
 * @property string|null $transaction_id
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserOrder $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPayment whereUuid($value)
 * @mixin \Eloquent
 */
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
