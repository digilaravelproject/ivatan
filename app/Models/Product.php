<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
