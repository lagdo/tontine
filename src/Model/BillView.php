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

    /**
     * @return Attribute
     */
    protected function inSession(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->bill_type === Bill::TYPE_LIBRE ||
                $this->bill_type === Bill::TYPE_SESSION,
        );
    }

    /**
     * @return Attribute
     */
    protected function inRound(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->bill_type === Bill::TYPE_ROUND ||
                $this->bill_type === Bill::TYPE_ONETIME,
        );
    }

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
        return $query->where('bill_type', Bill::TYPE_SESSION)
            ->where('session_id', $session->id);
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeOfTypeNotSession(Builder $query, Session $session): Builder
    {
        return $query->where('bill_type', '!=', Bill::TYPE_SESSION)
            ->where('round_id', $session->round_id)
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
