<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

class Deposit extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_deposits';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeWhereSession(Builder $query, Session $session): Builder
    {
        return $query->where('session_id', $session->id);
    }
}
