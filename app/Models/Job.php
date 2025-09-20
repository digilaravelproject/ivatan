<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $posted_by
 * @property string $title
 * @property string|null $description
 * @property string|null $company_name
 * @property string|null $location
 * @property int|null $salary_from
 * @property int|null $salary_to
 * @property string|null $employment_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $poster
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Database\Factories\JobFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job wherePostedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSalaryFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSalaryTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job withoutTrashed()
 * @mixin \Eloquent
 */
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
