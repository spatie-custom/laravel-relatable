<?php

namespace Spatie\Relatable\Test\TestModels;

use Illuminate\Database\Eloquent\Model;

class Lime extends Model
{
    /** @var array */
    protected $guarded = [];

    /** @var bool */
    public $timestamps = false;
}
