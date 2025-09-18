<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes, HasFactory, HasViews;
    protected $table = 'user_jobs';
    protected $fillable = [
        'posted_by',
        'title',
        'description',
        'company_name',
        'location',
        'salary_from',
        'salary_to',
        'employment_type',
        'status',
    ];

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
