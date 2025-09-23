<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Bill extends Base
{
    /**
     * @const
     */
    public const TYPE_LIBRE = 0;

    /**
     * @const
     */
    public const TYPE_SESSION = 1;

    /**
     * @const
     */
    public const TYPE_ROUND = 2;

    /**
     * @const
     */
    public const TYPE_ONETIME = 3;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'deadline' => 'datetime',
        ];
    }

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

    public function onetime_bill()
    {
        return $this->hasOne(OnetimeBill::class);
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
        return $query->select(['bills.*', 'v.*', DB::raw('s.day_date as bill_date')])
            ->join(DB::raw('v_bills as v'), 'v.bill_id', '=', 'bills.id')
            ->join(DB::raw('sessions as s'), 'v.session_id', '=', 's.id')
            ->where(function($query) use($session) {
                $query->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_LIBRE)
                        ->where('v.round_id', $session->round_id)
                        ->where('s.day_date', '<=', $session->day_date);
                })
                ->orWhere(function($query) use($session) {
                    $query->where('v.bill_type', self::TYPE_SESSION)
                        ->where('v.session_id', $session->id);
                })
                ->orWhere(function($query) use($session) {
                    $query->whereIn('v.bill_type', [self::TYPE_ROUND, self::TYPE_ONETIME])
                        ->where('v.round_id', $session->round_id);
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
     * @param  Builder  $query
     * @param Session $session
     *
     * @return Builder
     */
    public function scopeForSession(Builder $query, Session $session): Builder
    {
        return $this->scopeOfSession($query, $session)
            ->join(DB::raw('members as m'), 'v.member_id', '=', 'm.id')
            ->join(DB::raw('member_defs as md'), 'm.def_id', '=', 'md.id')
            ->select(['bills.*', 'v.*',
                DB::raw('md.name as member'), DB::raw('s.day_date as bill_date')]);
    }

    /**
     * @param Builder $query
     * @param Session $session
     *
     * @return Builder
     */
    public function scopeOfRound(Builder $query, Session $session): Builder
    {
        return $query->select(['bills.*', 'v.*', DB::raw('s.day_date as bill_date')])
            ->join(DB::raw('v_bills as v'), 'v.bill_id', '=', 'bills.id')
            ->join(DB::raw('sessions as s'), 'v.session_id', '=', 's.id')
            ->where(function($query) use($session) {
                $query->orWhere(function($query) use($session) {
                    $query->whereIn('v.bill_type', [self::TYPE_LIBRE, self::TYPE_SESSION])
                        ->where('v.round_id', $session->round_id)
                        ->where('s.day_date', '<', $session->day_date);
                })
                ->orWhere(function($query) use($session) {
                    $query->whereIn('v.bill_type', [self::TYPE_ROUND, self::TYPE_ONETIME])
                        ->where('v.round_id', $session->round_id);
                });
            });
    }

    /**
     * @param Builder $query
     * @param Charge $charge
     * @param bool $withLibre
     *
     * @return Builder
     */
    public function scopeOfCharge(Builder $query, Charge $charge, bool $withLibre): Builder
    {
        return $query->addSelect(['bills.*'])
            ->where(function($query) use($charge, $withLibre) {
                $query
                    ->orWhereHas('onetime_bill', fn(Builder $qb) =>
                        $qb->where('charge_id', $charge->id))
                    ->orWhereHas('round_bill', fn(Builder $qb) =>
                        $qb->where('charge_id', $charge->id))
                    ->orWhereHas('session_bill', fn(Builder $qb) =>
                        $qb->where('charge_id', $charge->id))
                    ->when($withLibre, fn($qw) =>
                        $qw->orWhereHas('libre_bill', fn(Builder $qb) =>
                            $qb->where('charge_id', $charge->id)));
            });
    }

    /**
     * @param  Builder  $query
     * @param  string $search
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->when($search !== '', fn($qs) => $qs
            ->where(DB::raw('lower(member)'), 'like', "%{$search}%"));
    }

    /**
     * @param  Builder  $query
     * @param  bool  $lendable
     *
     * @return Builder
     */
    public function scopeLendable(Builder $query, bool $lendable): Builder
    {
        return $query->where('lendable', $lendable);
    }
}
