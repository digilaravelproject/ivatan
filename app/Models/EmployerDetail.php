<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'company_name',
        'industry',
        'company_size',
        'company_website',
        'company_phone',
        'company_address',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
