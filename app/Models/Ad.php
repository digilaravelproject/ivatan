<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $ad_package_id
 * @property string $title
 * @property string|null $description
 * @property array<array-key, mixed>|null $media
 * @property string $status
 * @property Carbon|null $start_at
 * @property Carbon|null $end_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdImpression> $impressions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $impressions_count
 * @property-read \App\Models\AdPackage|null $package
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereAdPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereUserId($value)
 * @mixin \Eloquent
 */

class Ad extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'ad_package_id',
        'title',
        'description',
        'media_ids', // remove 'media' if you want Spatie to handle it
        'status',
        'start_at',
        'end_at',
        'impressions',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'media_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(AdPackage::class, 'ad_package_id');
    }

    public function payments()
    {
        return $this->hasMany(AdPayment::class);
    }

    public function impressions()
    {
        return $this->hasMany(AdImpression::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'ad_interest');
    }
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'live')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }
}
