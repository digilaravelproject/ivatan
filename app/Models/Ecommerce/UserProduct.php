<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
