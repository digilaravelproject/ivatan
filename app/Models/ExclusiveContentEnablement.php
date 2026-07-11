<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExclusiveContentEnablement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fee_paid',
        'status',
        'override_platform_fee',
        'override_platform_fee_type',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
