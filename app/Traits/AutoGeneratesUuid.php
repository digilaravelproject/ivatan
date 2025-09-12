<?php

namespace App\Traits;
use Illuminate\Support\Str;
trait AutoGeneratesUuid
{
      protected static function bootAutoGeneratesUuid()
    {
        static::creating(function ($model) {
            // ✅ Check if model has 'uuid' as fillable and it's empty
            if (
                $model->isFillable('uuid') &&
                empty($model->uuid)
            ) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
