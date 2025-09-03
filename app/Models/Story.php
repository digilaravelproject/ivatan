<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'user_id',
        'media_url',
        'type',
        'expires_at',
        'is_archived',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($q)
    {
        return $q->where('expires_at', '>', Carbon::now());
    }
}
