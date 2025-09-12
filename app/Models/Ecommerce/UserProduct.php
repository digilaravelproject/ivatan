<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'seller_id',
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'cover_image',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->uuid = (string) Str::uuid();
            $product->slug = Str::slug($product->title) . '-' . Str::random(8);
        });
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function images()
    {
        return $this->hasMany(UserProductImage::class, 'product_id');
    }
}
