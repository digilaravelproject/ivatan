<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Model;


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
