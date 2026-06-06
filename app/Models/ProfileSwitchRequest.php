<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileSwitchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_profile_id',
        'to_profile_id',
        'to_profile_type',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
        'user_notes',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'from_profile_id');
    }

    public function toProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'to_profile_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
