<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Receivable extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notes',
        'session_id',
    ];

    public function paidLate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->deposit !== null &&
                $this->deposit->session_id !== $this->session_id,
        );
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function deposit_real()
    {
        return $this->hasOne(DepositReal::class);
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

    /**
     * @param  Builder  $query
     * @param  string $search
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('subscription', fn(Builder $qs) => $qs
            ->whereHas('member', fn(Builder $qm) => $qm->search($search)));
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('deposit');
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopePaidHere(Builder $query, Session $session): Builder
    {
        return $query->whereHas('deposit', fn(Builder $qd) =>
            $qd->whereSession($session));
    }

    /**
     * @param  Builder  $query
     * @param  Session  $session
     *
     * @return Builder
     */
    public function scopePaidEarlier(Builder $query, Session $session): Builder
    {
        return $query->whereHas('deposit', fn(Builder $dq) =>
            $dq->whereHas('session', fn(Builder $sq) =>
                $sq->succeedes($session, true)));
    }

    /**
     * @param  Builder  $query
     * @param  Session  $session
     *
     * @return Builder
     */
    public function scopePaidLater(Builder $query, Session $session): Builder
    {
        return $query->whereHas('deposit', fn(Builder $dq) =>
            $dq->whereHas('session', fn(Builder $sq) =>
                $sq->precedes($session, true)));
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereDoesntHave('deposit');
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopePrecedes(Builder $query, Session $session): Builder
    {
        return $query
            ->whereHas('session', fn(Builder $sq) =>
                $sq->precedes($session, true));
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeSucceedes(Builder $query, Session $session): Builder
    {
        return $query
            ->whereHas('session', fn(Builder $sq) =>
                $sq->succeedes($session, true));
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeLate(Builder $query, Session $session): Builder
    {
        return $query->precedes($session)
            ->where(fn(Builder $oq) => $oq
                ->orWhere(fn(Builder $uq) => $uq->unpaid())
                ->orWhere(fn(Builder $pq) => $pq->paidHere($session)));
    }

    /**
     * @param  Builder  $query
     * @param  Session $session
     *
     * @return Builder
     */
    public function scopeEarly(Builder $query, Session $session): Builder
    {
        return $query->succeedes($session)
            ->where(fn(Builder $oq) => $oq
                ->orWhere(fn(Builder $uq) => $uq->unpaid())
                ->orWhere(fn(Builder $pq) => $pq->paidHere($session)));
    }
}
