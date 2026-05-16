<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Category has many interests
    public function interests()
    {
        return $this->hasMany(Interest::class);
    }
}
