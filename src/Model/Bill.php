<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Bill extends Base
{
    /**
     * @const
     */
    const TYPE_LIBRE = 0;

    /**
     * @const
     */
    const TYPE_SESSION = 1;

    /**
     * @const
     */
    const TYPE_ROUND = 2;

    /**
     * @const
     */
    const TYPE_TONTINE = 3;

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
        'charge',
        'amount',
        'lendable',
        'issued_at',
        'deadline',
        'notes',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'issued_at',
        'deadline',
    ];


    /**
     * @return Attribute
     */
    protected function libre(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->bill_type === self::TYPE_LIBRE,
        );
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function settlement()
    {
        return $this->hasOne(Settlement::class);
    }

    public function tontine_bill()
    {
        return $this->hasOne(TontineBill::class);
    }

    public function round_bill()
    {
        return $this->hasOne(RoundBill::class);
    }

    public function session_bill()
    {
        return $this->hasOne(SessionBill::class);
    }

    public function libre_bill()
    {
        return $this->hasOne(LibreBill::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('settlement');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereDoesntHave('settlement');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeLibre(Builder $query): Builder
    {
        return $query->where('v.bill_type', self::TYPE_LIBRE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFixed(Builder $query): Builder
    {
        return $query->where('v.bill_type', '!=', self::TYPE_LIBRE);
    }

    /**
     * @param  Builder  $query
     * @param Session $session
     *
     * @return Builder
     */
    public function scopeOfSession(Builder $query, Session $session): Builder
    {
        return $query->addSelect(['bills.*', 'v.*', DB::raw('s.start_at as bill_date')])
            ->join(DB::raw('v_bills as v'), 'v.bill_id', '=', 'bills.id')
            ->join(DB::raw('sessions as s'), 'v.session_id', '=', 's.id')
            ->where(function($query) use($session) {
                $query->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_LIBRE)
                        ->where('v.round_id', $session->round_id)
                        ->whereDate('s.start_at', '<=', $session->start_at);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_SESSION)
                        ->where('v.session_id', $session->id);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_ROUND)
                        ->where('v.round_id', $session->round_id);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_TONTINE)
                        ->where('v.tontine_id', $session->round->tontine_id)
                        ->whereDate('s.start_at', '<=', $session->start_at);
                });
            })
            ->where(function(Builder $query) use($session) {
                // The bills that are not yet paid, or that are paid in this round.
                $query->orWhere(function(Builder $query) {
                    $query->whereDoesntHave('settlement');
                })
                ->orWhere(function(Builder $query) use($session) {
                    $query->whereHas('settlement', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                });
            });
    }

    /**
     * @param Builder $query
     * @param Session $session
     *
     * @return Builder
     */
    public function scopeOfRound(Builder $query, Session $session): Builder
    {
        return $query->addSelect(['bills.*', 'v.*', DB::raw('s.start_at as bill_date')])
            ->join(DB::raw('v_bills as v'), 'v.bill_id', '=', 'bills.id')
            ->join(DB::raw('sessions as s'), 'v.session_id', '=', 's.id')
            ->where(function($query) use($session) {
                $query->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_LIBRE)
                        ->where('v.round_id', $session->round_id)
                        ->whereDate('s.start_at', '<', $session->start_at);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_SESSION)
                        ->where('v.round_id', $session->round_id)
                        ->whereDate('s.start_at', '<', $session->start_at);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_ROUND)
                        ->where('v.round_id', $session->round_id);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_TONTINE)
                        ->where('v.tontine_id', $session->round->tontine_id)
                        ->where(function($query) use($session) {
                            $query->orWhere(function(Builder $query) {
                                $query->whereDoesntHave('settlement');
                            })
                            ->orWhere(function(Builder $query) use($session) {
                                $query->whereHas('settlement', function(Builder $query) use($session) {
                                    $query->whereHas('session', function(Builder $query) use($session) {
                                        $query->where('round_id', $session->round_id);
                                    });
                                });
                            });
                        });
                });
            });
    }
}
