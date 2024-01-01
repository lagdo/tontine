<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    /**
     * @param  Builder  $query
     * @param  int  $page
     * @param  int  $limit
     *
     * @return Builder
     */
    public function scopePage(Builder $query, int $page, int $limit): Builder
    {
        return $page < 1 || $limit < 1 ? $query :
            $query->take($limit)->skip($limit * ($page - 1));
    }
}
