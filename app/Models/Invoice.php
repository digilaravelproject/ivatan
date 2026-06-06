<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_subscription_id',
        'user_id',
        'subscription_plan_id',
        'amount',
        'currency',
        'status',
        'items',
        'gateway_invoice_id',
        'payment_method',
        'paid_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'items' => 'array',
            'paid_at' => 'datetime',
            'due_date' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'user_subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', now());
    }

    public static function generateNumber(): string
    {
        $year = now()->format('Y');
        $last = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        if ($last) {
            $seq = (int) substr($last, -5) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('INV-%s-%05d', $year, $seq);
    }

    public function markAsPaid(?string $gatewayInvoiceId = null, ?string $paymentMethod = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_invoice_id' => $gatewayInvoiceId ?? $this->gateway_invoice_id,
            'payment_method' => $paymentMethod ?? $this->payment_method,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
