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
        'active',
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

    public function fine_bills()
    {
        return $this->hasMany(FineBill::class);
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

    /*
     * The fields that are used in the 4 following methods are filled by the
     * Siak\Tontine\Service\Charge\FeeService::getFees() and
     * Siak\Tontine\Service\Charge\FineService::getFines() calls.
     *
     * So they will hold valid values only for models that are returned by these calls.
     */

    /*
     * For a charge of type TYPE_FINE, only the fine_bills_count field is set.
     *
     * For a charge of type TYPE_FEE, the session_bills_count, round_bills_count
     * and tontine_bills_count fields are set, but only one of them can have a value
     * different than 0. So their sum returns this value.
     */
    public function getBillsCountAttribute()
    {
        return $this->is_fine ? $this->fine_bills_count :
            $this->session_bills_count + $this->round_bills_count + $this->tontine_bills_count;
    }

    /*
     * The above rules also apply for paid_* fields.
     */
    public function getPaidBillsCountAttribute()
    {
        return $this->is_fine ? $this->paid_fine_bills_count :
            $this->paid_session_bills_count + $this->paid_round_bills_count + $this->paid_tontine_bills_count;
    }

    /*
     * For a charge of type TYPE_FINE, only the all_fine_bills_count field is set.
     *
     * For a charge of type TYPE_FEE and period PERIOD_SESSION, only the
     * all_session_bills_count field is set.
     *
     * For the other charges, there is no all_* field because they are not session-specific.
     */
    public function getAllBillsCountAttribute()
    {
        return $this->is_fine ? $this->all_fine_bills_count :
            ($this->period_session ? $this->all_session_bills_count :
            $this->round_bills_count + $this->tontine_bills_count);
    }

    /*
     * The above rules also apply for all_paid_* fields.
     */
    public function getAllPaidBillsCountAttribute()
    {
        return $this->is_fine ? $this->all_paid_fine_bills_count :
            ($this->period_session ? $this->all_paid_session_bills_count :
            $this->paid_round_bills_count + $this->paid_tontine_bills_count);
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
}
