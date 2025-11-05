<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LibreBill extends Base
{
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

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
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

    /**
     * @param  Builder  $query
     * @param  Charge  $charge
     *
     * @return Builder
     */
    public function scopeWhereCharge(Builder $query, Charge $charge): Builder
    {
        return $query->where('charge_id', $charge->id);
    }

    /**
     * @param  Builder  $query
     * @param  Collection  $members
     *
     * @return Builder
     */
    public function scopeWhereMembers(Builder $query, Collection $members): Builder
    {
        return $query->whereIn('member_id', $members);
    }
}
