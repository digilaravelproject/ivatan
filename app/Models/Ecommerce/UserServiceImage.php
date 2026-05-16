<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $service_id
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserService $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserServiceImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
