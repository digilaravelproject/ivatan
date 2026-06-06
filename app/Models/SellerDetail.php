<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'seller_type',
        'business_name',
        'business_description',
        'business_email',
        'business_phone',
        'business_address',
    ];

    protected function casts(): array
    {
        return [
            'seller_type' => 'string',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function sellsProducts(): bool
    {
        return in_array($this->seller_type, ['products', 'both']);
    }

    public function sellsServices(): bool
    {
        return in_array($this->seller_type, ['services', 'both']);
    }

    public function sellsBoth(): bool
    {
        return $this->seller_type === 'both';
    }
}
