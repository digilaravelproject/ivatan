<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserSellerTransaction extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'opening_balance',
        'closing_balance',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
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
        return $this->belongsTo(\App\Models\User::class);
    }
}
