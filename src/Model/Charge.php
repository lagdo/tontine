<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function intval;

class Charge extends Base
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
        'round_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Also select fields from the charge_defs table.
        static::addGlobalScope('def', fn(Builder $query) => $query
            ->addSelect(['charges.*', 'd.name', 'd.type', 'd.period', 'd.amount', 'd.lendable'])
            ->join(DB::raw('charge_defs as d'), 'd.id', '=', 'charges.def_id'));
    }

    public function isFee(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->type) === ChargeDef::TYPE_FEE,
        );
    }

    public function isFine(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->type) === ChargeDef::TYPE_FINE,
        );
    }

    public function periodOnce(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->period) === ChargeDef::PERIOD_ONCE,
        );
    }

    public function periodRound(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->period) === ChargeDef::PERIOD_ROUND,
        );
    }

    public function periodSession(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->period) === ChargeDef::PERIOD_SESSION,
        );
    }

    public function isFixed(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->period) !== ChargeDef::PERIOD_NONE,
        );
    }

    public function isVariable(): Attribute
    {
        return Attribute::make(
            get: fn() => intval($this->period) === ChargeDef::PERIOD_NONE,
        );
    }

    public function hasAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_fixed || $this->amount > 0,
        );
    }

    public function def()
    {
        return $this->belongsTo(ChargeDef::class, 'def_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function session_bills()
    {
        return $this->hasMany(SessionBill::class);
    }

    public function round_bills()
    {
        return $this->hasMany(RoundBill::class);
    }

    public function onetime_bills()
    {
        return $this->hasMany(OnetimeBill::class);
    }

    public function libre_bills()
    {
        return $this->hasMany(LibreBill::class);
    }

    public function targets()
    {
        return $this->hasMany(SettlementTarget::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFixed(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->fixed());
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeVariable(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->variable());
    }

    /**
     * @param  Builder  $query
     * @param  bool  $lendable
     *
     * @return Builder
     */
    public function scopeLendable(Builder $query, bool $lendable): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) =>
            $qd->where('lendable', $lendable));
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFee(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->fee());
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFine(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->fine());
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeOnce(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->once());
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeRound(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->round());
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeSession(Builder $query): Builder
    {
        return $query->whereHas('def', fn(Builder $qd) => $qd->session());
    }
}
