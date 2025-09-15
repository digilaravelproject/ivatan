<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserServiceImage extends Model
{
    use HasFactory;

    protected $table = 'user_service_images';

    protected $fillable = [
        'service_id',
        'image_path',
    ];

    public function service()
    {
        return $this->belongsTo(UserService::class, 'service_id');
    }
}
