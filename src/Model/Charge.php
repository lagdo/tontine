<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'name',
        'type',
        'period',
        'amount',
        'lendable',
        'def_id',
        'round_id',
    ];

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
        return $query->where('period', '!=', ChargeDef::PERIOD_NONE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeVariable(Builder $query): Builder
    {
        return $query->where('period', ChargeDef::PERIOD_NONE);
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

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFee(Builder $query): Builder
    {
        return $query->where('type', ChargeDef::TYPE_FEE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFine(Builder $query): Builder
    {
        return $query->where('type', ChargeDef::TYPE_FINE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeOnce(Builder $query): Builder
    {
        return $query->where('type', ChargeDef::TYPE_FEE)
            ->where('period', ChargeDef::PERIOD_ONCE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeRound(Builder $query): Builder
    {
        return $query->where('type', ChargeDef::TYPE_FEE)
            ->where('period', ChargeDef::PERIOD_ROUND);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeSession(Builder $query): Builder
    {
        return $query->where('type', ChargeDef::TYPE_FEE)
            ->where('period', ChargeDef::PERIOD_SESSION);
    }
}
