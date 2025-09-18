<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdImpression extends Model
{
    use HasFactory;


    protected $fillable = [
        'ad_id',
        'user_id',
        'ip_address',
    ];


    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
