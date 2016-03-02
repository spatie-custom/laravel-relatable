<?php

namespace Spatie\Relatable\Test\TestModels;

use Illuminate\Database\Eloquent\Model;
use Spatie\Relatable\HasRelatedContent;

class HasFruitAsRelatedContent extends Model
{
    use HasRelatedContent;

    /** @var array */
    protected $guarded = [];

    /** @var bool */
    public $timestamps = false;
}
