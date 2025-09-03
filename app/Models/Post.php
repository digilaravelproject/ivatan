<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'type',
        'media_metadata',
        'status',
        'visibility',
    ];

    protected $casts = [
        'media_metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
}
