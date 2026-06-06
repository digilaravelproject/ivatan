<?php

namespace App\Models;

use App\Models\Concerns\HasProfileDetails;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'is_active',
        'is_default',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sellerDetails(): HasOne
    {
        return $this->hasOne(SellerDetail::class);
    }

    public function employerDetails(): HasOne
    {
        return $this->hasOne(EmployerDetail::class);
    }

    public function musicDetails(): HasOne
    {
        return $this->hasOne(MusicPlaylistDetail::class);
    }

    public function creatorDetails(): HasOne
    {
        return $this->hasOne(ContentCreatorDetail::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->whereIn('status', ['active', 'past_due'])
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            })
            ->latestOfMany();
    }

    public function switchRequests(): HasMany
    {
        return $this->hasMany(ProfileSwitchRequest::class, 'from_profile_id');
    }

    public function getDetailAttribute()
    {
        return match ($this->type) {
            'seller' => $this->sellerDetails,
            'employer' => $this->employerDetails,
            'music' => $this->musicDetails,
            'creator' => $this->creatorDetails,
            default => null,
        };
    }

    public function isPersonal(): bool
    {
        return $this->type === 'personal';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function canBeActivated(): bool
    {
        return !in_array($this->status, ['pending_approval', 'suspended']);
    }

    public function isActiveProfile(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending_approval');
    }
}
