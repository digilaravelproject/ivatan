<?php
namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Jobs\UserEmployment;
use App\Models\Jobs\UserEducation;

class UserProfile extends Model
{
    protected $fillable = ['user_id', 'resume_headline', 'skills_list', 'contact_no'];

    protected $casts = [
        'skills_list' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employments()
    {
        return $this->morphMany(UserEmployment::class, 'employable');
    }

    public function educations()
    {
        return $this->morphMany(UserEducation::class, 'educationable');
    }
}
