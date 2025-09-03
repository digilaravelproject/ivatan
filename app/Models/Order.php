<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'buyer_id',
        'total_amount',
        'shipping_address',
        'status',
        'placed_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'total_amount' => 'decimal:2',
        'placed_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
