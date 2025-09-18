<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdPackage extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',    // virtual field mapped to name
        'name',     // actual DB field
        'description',
        'price',
        'currency',
        'reach_limit',
        'duration_days',
        'targeting',
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'targeting' => 'array',
    ];
    // Virtual attribute: `title`
    public function getTitleAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
