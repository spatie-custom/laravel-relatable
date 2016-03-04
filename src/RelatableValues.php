<?php

namespace Spatie\Relatable;

use Illuminate\Database\Eloquent\Model;

class RelatableValues
{
    /** @var string */
    protected $sourceType;

    /** @var int */
    protected $sourceId;

    /** @var string */
    protected $relatedType;

    /** @var int */
    protected $relatedId;

    public function __construct(
        string $sourceType,
        int $sourceId,
        string $relatedType,
        int $relatedId
    ) {
        $this->sourceType = $sourceType;
        $this->sourceId = $sourceId;
        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
    }

    public function equals(RelatableValues $relatableValues) : bool
    {
        return $this->toArray() === $relatableValues->toArray();
    }

    public function toArray() : array
    {
        return [
            'source_type' => $this->sourceType,
            'source_id' => $this->sourceId,
            'related_type' => $this->relatedType,
            'related_id' => $this->relatedId,
        ];
    }
}
