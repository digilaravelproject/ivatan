<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentCreatorDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'channel_name',
        'content_category',
        'platform',
        'bio',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
