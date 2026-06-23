<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_implemented',
    ];

    protected $casts = [
        'is_implemented' => 'boolean',
    ];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_features', 'feature_id', 'subscription_plan_id')
            ->withPivot('limit_value')
            ->withTimestamps();
    }
}
