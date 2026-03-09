<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserEnquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'seller_id',
        'service_id',
        'product_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\User::class, 'seller_id');
    }

    public function service()
    {
        return $this->belongsTo(UserService::class, 'service_id');
    }

    public function product()
    {
        return $this->belongsTo(UserProduct::class, 'product_id');
    }
}
