<?php

namespace Siak\Tontine\Model;

use Database\Factories\ChargeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Siak\Tontine\Model\Traits\HasCurrency;

use function intval;

class Charge extends Model
{
    use HasFactory;
    use HasCurrency;

    /**
     * @var int
     */
    public static $memberCount = 0;

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

    public function bills()
    {
        return $this->hasMany(Bill::class);
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

    public function getCurrSettlementCount(array $settlements)
    {
        return $settlements['current'][$this->id] ?? 0;
    }

    public function getPrevSettlementCount(array $settlements)
    {
        return $settlements['previous'][$this->id] ?? 0;
    }

    public function getCurrBillCount(array $bills)
    {
        // For fees, there's a single bill for all tontine members.
        // The number of bills then needs to be multiplied by the number of members.
        $count = $this->is_fee ? self::$memberCount : 1;
        return $count * ($bills['current'][$this->id] ?? 0);
    }

    public function getPrevBillCount(array $bills)
    {
        // For fees, there's a single bill for all tontine members.
        // The number of bills then needs to be multiplied by the number of members.
        $count = $this->is_fee ? self::$memberCount : 1;
        return $count * ($bills['previous'][$this->id] ?? 0);
    }
}
