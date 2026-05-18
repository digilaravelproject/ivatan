<?php

namespace App\Models;

use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatParticipant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiveChatGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'chat_mode',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($group) {
            if (empty($group->slug)) {
                $group->slug = Str::slug($group->name) . '-' . Str::random(4);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chat()
    {
        return $this->hasOne(UserChat::class, 'live_chat_group_id');
    }

    public function participants()
    {
        return $this->hasManyThrough(
            UserChatParticipant::class,
            UserChat::class,
            'live_chat_group_id',
            'chat_id',
            'id',
            'id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
