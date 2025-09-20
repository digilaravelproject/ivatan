<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $buyer_id
 * @property numeric $total_amount
 * @property string $status
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserAddress|null $address
 * @property-read User $buyer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Ecommerce\UserPayment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Ecommerce\UserShipping|null $shipping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrder whereUuid($value)
 * @mixin \Eloquent
 */
class UserOrder extends Model
{
    protected $table = 'user_orders';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';

    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_PENDING = 'pending';

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
