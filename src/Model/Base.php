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
        return $query->when($limit > 0, function($query) use($limit) {
            return $query->take($limit);
        })->when($page > 0 && $limit > 0, function($query) use($page, $limit) {
            return $query->skip($limit * ($page - 1));
        });
    }
}
