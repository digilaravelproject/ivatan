<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ecommerce\UserProduct $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProductImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'image_path'];

    public function product()
    {
        return $this->belongsTo(UserProduct::class, 'product_id');
    }
}
