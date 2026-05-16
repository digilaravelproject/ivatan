<?php
namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class UserEmployment extends Model
{
    protected $table = 'user_employments';

    protected $fillable = [
        'is_current_employment', 'company_name', 'job_title',
        'joining_date', 'worked_till', 'job_description'
    ];

    protected $casts = [
        'is_current_employment' => 'boolean',
        'joining_date' => 'date',
        'worked_till' => 'date',
    ];

    public function employable()
    {
        return $this->morphTo();
    }
}
