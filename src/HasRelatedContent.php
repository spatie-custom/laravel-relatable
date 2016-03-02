<?php

namespace Spatie\Relatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property \Illuminate\Database\Eloquent\Collection $related
 * @property \Illuminate\Database\Eloquent\Collection $relatables
 */
trait HasRelatedContent
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function relatables() : MorphMany
    {
        return $this->morphMany(Relatable::class, 'source');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelatedAttribute() : Collection
    {
        return $this->relatables
            ->groupBy(function (Relatable $relatable) {
                return $this->getActualClassNameForMorph($relatable->related_type);
            })
            ->flatMap(function (Collection $typeGroup, string $type) {
                return $type::whereIn('id', $typeGroup->pluck('related_id'))->get();
            });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return \Spatie\Relatable\Relatable
     */
    public function relate($item, string $type = '') : Relatable
    {
        return Relatable::firstOrCreate($this->getRelatableValues($item, $type));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return int
     */
    public function unrelate($item, string $type = '') : int
    {
        return Relatable::where($this->getRelatableValues($item, $type))->delete();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return array
     */
    protected function getRelatableValues($item, string $type = '') : array
    {
        if (! $item instanceof Model && empty($type)) {
            throw new \InvalidArgumentException('If an id is specified as an item, the type isn\'t allowed to be empty.');
        }

        return [
            'source_id' => $this->getKey(),
            'source_type' => $this->getMorphClass(),
            'related_id' => $item instanceof Model ? $item->getKey() : $item,
            'related_type' => $item instanceof Model ? $item->getMorphClass() : $type,
        ];
    }
}
