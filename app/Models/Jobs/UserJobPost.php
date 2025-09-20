<?php

namespace App\Models\Jobs;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $employer_id
 * @property string $title
 * @property string $slug
 * @property string|null $company_name
 * @property string|null $company_website
 * @property string|null $company_logo
 * @property string $description
 * @property string|null $responsibilities
 * @property string|null $requirements
 * @property string|null $location
 * @property string|null $country
 * @property string $employment_type
 * @property numeric|null $salary_min
 * @property numeric|null $salary_max
 * @property string $currency
 * @property bool $is_remote
 * @property string $status
 * @property-read int|null $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jobs\UserJobApplication> $applications
 * @property-read int|null $applications_count
 * @property-read User $employer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost filter(array $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCompanyWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereEmployerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereIsRemote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereResponsibilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSalaryMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSalaryMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserJobPost whereViewsCount($value)
 * @mixin \Eloquent
 */
class UserJobPost extends Model
{
    use HasFactory, AutoGeneratesUuid, HasViews;

    protected $table = 'user_job_posts';

    protected $fillable = [
        'uuid',
        'employer_id',
        'title',
        'slug',
        'company_name',
        'company_website',
        'company_logo',
        'description',
        'responsibilities',
        'requirements',
        'location',
        'country',
        'employment_type',
        'salary_min',
        'salary_max',
        'currency',
        'is_remote',
        'status',
    ];

    protected $casts = [
        'salary_min'  => 'decimal:2',
        'salary_max'  => 'decimal:2',
        'is_remote'   => 'boolean',
        'views_count' => 'integer',
    ];



    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(UserJobApplication::class, 'job_id');
    }

    /**
     * Simple filter scope. Accepts array of filters:
     * ['q','location','employment_type','is_remote','salary_min','salary_max','country']
     */
    public function scopeFilter($query, array $filters = [])
    {
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (! empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (isset($filters['is_remote'])) {
            $query->where('is_remote', (bool) $filters['is_remote']);
        }

        if (! empty($filters['salary_min'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('salary_min')->orWhere('salary_min', '>=', $filters['salary_min']);
            });
        }

        if (! empty($filters['salary_max'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('salary_max')->orWhere('salary_max', '<=', $filters['salary_max']);
            });
        }

        return $query;
    }
}
