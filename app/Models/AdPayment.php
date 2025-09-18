<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdPayment extends Model
{
    use HasFactory;


    protected $fillable = [
        'ad_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'meta',
    ];


    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];


    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
