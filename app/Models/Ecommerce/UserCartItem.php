<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property int $cart_id
 * @property int $seller_id
 * @property string|null $item_type
 * @property int|null $item_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserCart $cart
 * @property-read Model|\Eloquent|null $item
 * @property-read Model|\Eloquent|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCartItem whereUuid($value)
 * @mixin \Eloquent
 */
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
    public function product_old()
    {
        return $this->morphTo(UserProduct::class, 'item_type', 'item_id');
    }
    /**
     * Get the product associated with the cart item.
     * It checks item_type to decide whether to fetch product or service.
     */
    public function product()
    {
        return $this->belongsTo(UserProduct::class, 'item_id');  // Assuming 'item_id' is the column linking to 'user_products'
    }

    public function service()
    {
        return $this->belongsTo(UserService::class, 'item_id');  // Assuming 'item_id' is the column linking to 'user_services'
    }
}
