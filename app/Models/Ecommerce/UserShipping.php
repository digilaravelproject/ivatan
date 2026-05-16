<?php

namespace App\Models\Ecommerce;

use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property string|null $provider
 * @property string|null $tracking_number
 * @property string $status
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserOrder $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserShipping whereUuid($value)
 * @mixin \Eloquent
 */
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
