<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
