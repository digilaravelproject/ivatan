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
 * @property int $stock
 * @property string|null $cover_image
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserCartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ecommerce\UserOrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereAdminNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProduct whereUuid($value)
 * @mixin \Eloquent
 */
class UserProduct extends Model
{
    use HasFactory,AutoGeneratesUuid;
    protected $fillable = [
        'uuid',
        'seller_id',
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'cover_image',
        'status'
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
        return $this->hasMany(UserProductImage::class, 'product_id');
    }
    public function cartItems()
    {
        return $this->hasMany(UserCartItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(UserOrderItem::class, 'item_id')->where('item_type', 'user_products');
    }
}
