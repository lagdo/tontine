<?php

namespace Siak\Tontine\Model;

use Database\Factories\ChargeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Siak\Tontine\Model\Traits\HasCurrency;

use function intval;

class Charge extends Base
{
    use HasFactory;
    use HasCurrency;

    /**
     * @const
     */
    const TYPE_FEE = 0;

    /**
     * @const
     */
    const TYPE_FINE = 1;

    /**
     * @const
     */
    const PERIOD_NONE = 0;

    /**
     * @const
     */
    const PERIOD_ONCE = 1;

    /**
     * @const
     */
    const PERIOD_ROUND = 2;

    /**
     * @const
     */
    const PERIOD_SESSION = 3;

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
        'active',
        'lendable',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return ChargeFactory::new();
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function session_bills()
    {
        return $this->hasMany(SessionBill::class);
    }

    public function round_bills()
    {
        return $this->hasMany(RoundBill::class);
    }

    public function tontine_bills()
    {
        return $this->hasMany(TontineBill::class);
    }

    public function libre_bills()
    {
        return $this->hasMany(LibreBill::class);
    }

    public function targets()
    {
        return $this->hasMany(SettlementTarget::class);
    }

    public function getBillsCountAttribute()
    {
        return $this->tontine_bills_count + $this->round_bills_count +
            $this->session_bills_count + $this->libre_bills_count;
    }

    public function getIsFeeAttribute()
    {
        return intval($this->type) === self::TYPE_FEE;
    }

    public function getIsFineAttribute()
    {
        return intval($this->type) === self::TYPE_FINE;
    }

    public function getPeriodOnceAttribute()
    {
        return intval($this->period) === self::PERIOD_ONCE;
    }

    public function getPeriodRoundAttribute()
    {
        return intval($this->period) === self::PERIOD_ROUND;
    }

    public function getPeriodSessionAttribute()
    {
        return intval($this->period) === self::PERIOD_SESSION;
    }

    public function getIsFixedAttribute()
    {
        return intval($this->period) !== self::PERIOD_NONE;
    }

    public function getIsVariableAttribute()
    {
        return intval($this->period) === self::PERIOD_NONE;
    }

    public function getHasAmountAttribute()
    {
        return $this->is_fixed || $this->amount > 0;
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFee(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FEE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFine(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FINE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeOnce(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FEE)->where('period', self::PERIOD_ONCE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeRound(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FEE)->where('period', self::PERIOD_ROUND);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeSession(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FEE)->where('period', self::PERIOD_SESSION);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFixed(Builder $query): Builder
    {
        return $query->where('period', '!=', self::PERIOD_NONE)->where('active', true);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeVariable(Builder $query): Builder
    {
        return $query->where('period', self::PERIOD_NONE)->where('active', true);
    }
}
