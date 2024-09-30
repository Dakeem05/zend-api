<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait CreateUuid
{
    public static function bootCreateUuid()
    {
        static::creating(function (Model $model) {
            if ($model->uuid === null || $model->uuid === '') {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}