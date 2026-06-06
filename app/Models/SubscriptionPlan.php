<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_type',
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'duration_days',
        'features',
        'is_active',
        'is_default',
        'sort_order',
        'gateway_plan_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function scopeForProfileType(Builder $query, string $type): Builder
    {
        return $query->where('profile_type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function isFree(): bool
    {
        return $this->price <= 0;
    }

    public function isPaid(): bool
    {
        return $this->price > 0;
    }
}
