<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'status',
        'cancelled_at',
        'cancellation_reason',
        'gateway_subscription_id',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_response',
        'next_billing_at',
        'auto_renew',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'auto_renew' => 'boolean',
            'gateway_response' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_subscription_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'past_due'])
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            });
    }

    public function scopeForProfile(Builder $query, int $profileId): Builder
    {
        return $query->where('profile_id', $profileId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNeedRenewal(Builder $query): Builder
    {
        return $query->where('auto_renew', true)
            ->where('status', 'active')
            ->whereNotNull('next_billing_at')
            ->where('next_billing_at', '<=', now()->addDay());
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'past_due'])
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->ends_at !== null && $this->ends_at->isPast());
    }

    public function daysRemaining(): int
    {
        if ($this->ends_at === null) {
            return PHP_INT_MAX;
        }

        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function cancel(?string $reason = null, string $mode = 'end_of_period'): void
    {
        $data = [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'auto_renew' => false,
        ];

        if ($mode === 'immediate') {
            $data['ends_at'] = now();
            $data['next_billing_at'] = null;
        }

        $this->update($data);
    }

    public function generateInvoice(): Invoice
    {
        $plan = $this->plan;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'user_subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'status' => 'pending',
            'items' => [
                [
                    'description' => $plan->name,
                    'amount' => $plan->price,
                    'period' => $plan->duration_days . ' days',
                ],
            ],
            'due_date' => $this->next_billing_at ?? $this->ends_at,
        ]);

        return $invoice;
    }
}
