<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

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
    public function scopeLate(Builder $query, Session $session): Builder
    {
        return $query
            ->whereHas('session', fn(Builder $sq) =>
                $sq->precedes($session, true))
            ->where(fn(Builder $lq) => $lq
                ->orWhereDoesntHave('deposit')
                ->orWhereHas('deposit', fn(Builder $dq) => $dq
                    ->where('session_id', $session->id)));
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
}
