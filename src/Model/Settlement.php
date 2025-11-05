<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

class Settlement extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * @param  Builder  $query
     * @param  Session  $session
     *
     * @return Builder
     */
    public function scopeWhereSession(Builder $query, Session $session): Builder
    {
        return $query->where('session_id', $session->id);
    }
}
