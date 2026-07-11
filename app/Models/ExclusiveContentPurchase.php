<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExclusiveContentPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'buyer_id',
        'user_post_id',
        'creator_price',
        'platform_fee_charged',
        'gateway_charge_amount',
        'gateway_charge_bearer',
        'final_paid_amount',
        'gateway_transaction_id',
        'gateway',
        'status',
        'refunded_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'user_post_id');
    }

    public function access()
    {
        return $this->hasOne(ExclusiveContentAccess::class, 'purchase_id');
    }
}
