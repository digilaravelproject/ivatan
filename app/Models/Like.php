<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'likeable_type', 'likeable_id'];

    // Define the polymorphic relationship (this could be a Post or Comment)
    public function likeable()
    {
        return $this->morphTo();
    }

    // Get the user who liked the item
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
