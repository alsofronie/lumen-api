<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 05/08/2017
 * Time: 18:54
 */

namespace App\Models\Traits;

use App\Lib\Uuid;

trait UuidPrimaryKey
{
    /**
     * Function getIncrementing
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Function bootUuid
     *
     * @return void
     */
    public static function bootUuidPrimaryKey()
    {
        static::creating(function ($model) {
            $model->incrementing = false;
            $model->attributes[$model->getKeyName()] = (new Uuid())->bin;
        });
    }
}
