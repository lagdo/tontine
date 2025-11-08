<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BillView extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_bills';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

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
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeOfTypeSession(Builder $query, Session $session): Builder
    {
        return $query->where('v_bills.bill_type', Bill::TYPE_SESSION)
            ->where('v_bills.session_id', $session->id);
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeOfTypeNotSession(Builder $query, Session $session): Builder
    {
        return $query->where('v_bills.bill_type', '!=', Bill::TYPE_SESSION)
            ->where('v_bills.round_id', $session->round_id)
            ->whereHas('session', fn(Builder $qs) =>
                $qs->where('day_date', '<=', $session->day_date))
            ->whereHas('bill', fn(Builder $qb) =>
                // Take unsettled bills, or bills settled on this session..
                $qb->where(fn(Builder $qs) => $qs
                    ->orWhereDoesntHave('settlement')
                    ->orWhereHas('settlement', fn(Builder $qt) =>
                        $qt->where('session_id', $session->id))));
    }
}
