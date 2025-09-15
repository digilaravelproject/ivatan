<?php

namespace App\Models\Jobs;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobApplication extends Model
{
    use HasFactory,AutoGeneratesUuid;

    protected $table = 'user_job_applications';

    protected $fillable = [
        'uuid',
        'job_id',
        'applicant_id',
        'cover_message',
        'resume_path',
        'resume_media_id',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(UserJobPost::class, 'job_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }
}
