<?php

namespace Spatie\Relatable;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property EloquentCollection $related
 * @property EloquentCollection $relatables
 */
trait HasRelatedContent
{
    /** @var \Illuminate\Support\Collection|null */
    protected $relatableCache;

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
        if ($this->relatableCache === null) {
            $this->loadRelated();
        }

        return $this->relatableCache;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function loadRelated() : Collection
    {
        return $this->relatableCache = $this->relatables
            ->groupBy(function (Relatable $relatable) {
                return $this->getActualClassNameForMorph($relatable->related_type);
            })
            ->flatMap(function (Collection $typeGroup, string $type) {
                return $type::whereIn('id', $typeGroup->pluck('related_id'))->get();
            });
    }

    /**
     * @return bool
     */
    public function hasRelated() : bool
    {
        return ! $this->related->isEmpty();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return \Spatie\Relatable\Relatable
     */
    public function relate($item, string $type = '') : Relatable
    {
        return Relatable::firstOrCreate(
            $this->getRelatableValues($item, $type)->toArray()
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return int
     */
    public function unrelate($item, string $type = '') : int
    {
        return Relatable::where($this->getRelatableValues($item, $type)->toArray())->delete();
    }

    /**
     * The `$items` parameter can either contain an Eloquent collection of models, or an array
     * with the shape of [['id' => int, 'type' => string], ...].
     *
     * I'll gladly accept PR's optimizing queries here!
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $items
     * @param bool $detaching
     */
    public function syncRelated($items, $detaching = true)
    {
        $current = $this->relatables->map(function (Relatable $relatable) {
            return $relatable->toRelatableValues();
        });

        

        $attach = $items
            ->filter(function (array $values) use ($current) {
                return ! $current->contains($values);
            })
            ->toArray();

        if ($detaching) {
            $detach = $current
                ->filter(function (array $values) use ($items) {
                    return ! $items->contains($values);
                })
                ->toArray();

            $this->unrelateMany($detach);
        }

        $this->relateMany($attach);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return \Spatie\Relatable\RelatableValues
     */
    protected function getRelatableValues($item, string $type = '') : RelatableValues
    {
        if (! $item instanceof Model && empty($type)) {
            throw new \InvalidArgumentException(
                'If an id is specified as an item, the type isn\'t allowed to be empty.'
            );
        }

        return new RelatableValues(
            $this->getMorphClass(),
            $this->getKey(),
            $item instanceof Model ? $item->getMorphClass() : $type,
            $item instanceof Model ? $item->getKey() : $item
        );
    }
}
