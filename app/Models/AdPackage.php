<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property string $currency
 * @property int $reach_limit
 * @property int $duration_days
 * @property array<array-key, mixed>|null $targeting
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ad> $ads
 * @property-read int|null $ads_count
 * @property mixed $title
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereReachLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereTargeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdPackage extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',    // virtual field mapped to name
        'name',     // actual DB field
        'description',
        'price',
        'currency',
        'reach_limit',
        'duration_days',
        'targeting',
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'targeting' => 'array',
    ];
    // Virtual attribute: `title`
    public function getTitleAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
