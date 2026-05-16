<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCart whereUuid($value)
 * @mixin \Eloquent
 */
class UserCart extends Model
{
    protected $fillable = ['uuid', 'user_id'];

    public function items_old()
    {
        return $this->hasMany(UserCartItem::class, 'cart_id');
    }
    public function items()
    {
        return $this->hasMany(UserCartItem::class, 'cart_id');  // Ensure the foreign key is 'cart_id'
    }
}
