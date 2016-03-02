<?php

namespace Spatie\Relatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property \Illuminate\Database\Eloquent\Collection $relatedContentRelations
 */
trait HasRelatedContent
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function relatedContentRelations() : MorphMany
    {
        return $this->morphMany(RelatedContentRelation::class, 'source');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelatedContent() : Collection
    {
        return $this->relatedContentRelations
            ->groupBy(function (RelatedContentRelation $relatedContentRelation) {
                return $this->getActualClassNameForMorph($relatedContentRelation->related_type);
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
    public function relateContent($item, string $type = '') : Relatable
    {
        return RelatedContentRelation::firstOrCreate($this->getRelatedContentRelationValues($item, $type));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return int
     */
    public function unrelateContent($item, string $type = '') : int
    {
        return RelatedContentRelation::where($this->getRelatedContentRelationValues($item, $type))->delete();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $item
     * @param string|null $type
     *
     * @return array
     */
    protected function getRelatedContentRelationValues($item, string $type = '') : array
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
