<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $ad_id
 * @property int $user_id
 * @property numeric $amount
 * @property string $currency
 * @property string $status
 * @property string|null $razorpay_order_id
 * @property string|null $razorpay_payment_id
 * @property string|null $razorpay_signature
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ad $ad
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpayOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpayPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereRazorpaySignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPayment whereUserId($value)
 * @mixin \Eloquent
 */
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
