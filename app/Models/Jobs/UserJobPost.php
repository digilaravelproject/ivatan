<?php

namespace App\Models\Jobs;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserJobPost extends Model
{
    use HasFactory,AutoGeneratesUuid;

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
