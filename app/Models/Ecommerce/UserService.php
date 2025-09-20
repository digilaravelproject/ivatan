<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $seller_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property numeric $price
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserServiceImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereAdminNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserService whereUuid($value)
 * @mixin \Eloquent
 */
class UserService extends Model
{
    use HasFactory;
    use AutoGeneratesUuid;

    protected $table = 'user_services';

    protected $fillable = [
        'uuid',
        'seller_id',
        'title',
        'slug',
        'description',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];



    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function images()
    {
        return $this->hasMany(UserServiceImage::class, 'service_id');
    }

    public function cartItems()
    {
        return $this->hasMany(UserCartItem::class, 'item_id')->where('item_type', 'user_services');
    }

    public function orderItems()
    {
        return $this->hasMany(UserOrderItem::class, 'item_id')->where('item_type', 'user_services');
    }
}
