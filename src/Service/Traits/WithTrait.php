<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

use function count;
use function is_string;

trait WithTrait
{
    /**
     * @var array
     */
    protected array $withs = [];

    /**
     * @var array
     */
    protected array $withCounts = [];

    /**
     * @param array|string $relation
     *
     * @return self
     */
    public function with(array|string $relation): self
    {
        $this->withs = is_string($relation) ?
            [...$this->withs, $relation] :
            [...$this->withs, ...$relation];
        return $this;
    }

    /**
     * @param array|string $relation
     *
     * @return self
     */
    public function withCount(array|string $relation): self
    {
        $this->withCounts = is_string($relation) ?
            [...$this->withCounts, $relation] :
            [...$this->withCounts, ...$relation];
        return $this;
    }

    /**
     * @param Builder|Relation $query
     *
     * @return void
     */
    protected function addWith(Builder|Relation $query)
    {
        $query->when(count($this->withs) > 0, fn() => $query->with($this->withs))
            ->when(count($this->withCounts) > 0, fn() => $query->withCount($this->withCounts));
    }
}
