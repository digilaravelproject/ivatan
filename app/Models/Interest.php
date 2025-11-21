<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'interest_category_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'interest_user');
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function category()
    {
        return $this->belongsTo(InterestCategory::class, 'interest_category_id');
    }
}
