<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int|null $order_id
 * @property string $type
 * @property string $name
 * @property string $phone
 * @property string $address_line1
 * @property string|null $address_line2
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $postal_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUuid($value)
 * @mixin \Eloquent
 */
class UserAddress extends Model
{
    use AutoGeneratesUuid;
    protected $table = 'user_addresses';
    protected $fillable = [
        'uuid','user_id','order_id','type','name','phone',
        'address_line1','address_line2','city','state','country','postal_code'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
