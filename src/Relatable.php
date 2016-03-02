<?php

namespace Spatie\Relatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $source_id
 * @property string $source_type
 * @property int $related_id
 * @property string $related_type
 */
class Relatable extends Model
{
    /** @var array */
    protected $guarded = [];

    /** @var string|null */
    protected $primaryKey = null;

    /** @var bool */
    public $incrementing = false;

    /** @var bool */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function related() : MorphTo
    {
        return $this->morphTo('related');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function source() : MorphTo
    {
        return $this->morphTo('source');
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return config('laravel-relatable.table', 'relatables');
    }
}
