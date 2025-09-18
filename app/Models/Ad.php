<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ad extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'ad_package_id',
        'title',
        'description',
        'media',
        'status',
        'start_at',
        'end_at',
        'impressions',
    ];


    protected $casts = [
        'media' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
