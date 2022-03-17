<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

abstract class AutoUuidModel extends Model
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if(!$model->uuid) {
                $model->uuid = (string) Uuid::generate(4);
            }
        });
    }
}
