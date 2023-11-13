<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SlugTrait
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = Str::random(6);
        });
    }
}
