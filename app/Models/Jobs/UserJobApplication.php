<?php

namespace App\Models\Jobs;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property int $job_id
 * @property int $applicant_id
 * @property string|null $cover_message
 * @property string|null $resume_path
 * @property int|null $resume_media_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $applied_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $applicant
 * @property-read \App\Models\Jobs\UserJobPost $job
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereApplicantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereCoverMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereResumeMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereResumePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobApplication whereUuid($value)
 * @mixin \Eloquent
 */
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
