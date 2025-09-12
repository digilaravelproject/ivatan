<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'image_path'];

    public function product()
    {
        return $this->belongsTo(UserProduct::class, 'product_id');
    }
}
