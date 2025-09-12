<?php

namespace App\Models\Ecommerce;

use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class UserShipping extends Model
{
    use AutoGeneratesUuid;
    protected $table = 'user_shippings';
    protected $fillable = ['uuid', 'order_id', 'provider', 'tracking_number', 'status', 'meta'];
    protected $casts = ['meta' => 'array'];


    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'order_id');
    }
}
