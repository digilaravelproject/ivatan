<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $ad_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ad $ad
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdImpression whereUserId($value)
 * @mixin \Eloquent
 */
class AdImpression extends Model
{
    use HasFactory;


    protected $fillable = [
        'ad_id',
        'user_id',
        'ip_address',
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
