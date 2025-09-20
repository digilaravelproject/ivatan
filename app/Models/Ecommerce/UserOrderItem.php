<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property int $seller_id
 * @property string|null $item_type
 * @property int|null $item_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $item_model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOrderItem whereUuid($value)
 * @mixin \Eloquent
 */
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
