<?php
namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    protected $table = 'user_educations';

    protected $fillable = [
        'university_name', 'course_name', 'course_type',
        'course_duration', 'percentage_cgpa'
    ];

    public function educationable()
    {
        return $this->morphTo();
    }
}
