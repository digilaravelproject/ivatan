<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSellerFinancial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'account_type',
        'is_active',
    ];

    protected $casts = [
        'account_number' => 'encrypted',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
