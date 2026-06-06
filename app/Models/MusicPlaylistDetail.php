<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicPlaylistDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'artist_name',
        'stage_name',
        'genre',
        'label',
        'bio',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
