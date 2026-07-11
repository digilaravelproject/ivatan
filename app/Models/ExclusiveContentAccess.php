<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExclusiveContentAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_post_id',
        'purchase_id',
        'granted_at',
        'expires_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'user_post_id');
    }

    public function purchase()
    {
        return $this->belongsTo(ExclusiveContentPurchase::class, 'purchase_id');
    }

    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->greaterThan($this->expires_at);
    }
}
