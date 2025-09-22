<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;

use function trans;

/**
 * @property string $interest_type
 * @property float $interest_rate
 * @property-read int $principal
 * @property-read int $interest
 * @property-read bool $fixed_interest
 * @property-read bool $simple_interest
 * @property-read bool $compound_interest
 * @property-read int $refunds_count
 * @property-read Session $session
 * @property-read Member $member
 * @property-read Fund $fund
 * @property-read Debt $principal_debt
 * @property-read Debt $interest_debt
 * @property-read Collection $debts
 * @property-read Collection $refunds
 */
class Loan extends Base
{
    use Traits\DateFormatter;

    /**
     * @const
     */
    const INTEREST_FIXED = 'f';

    /**
     * @const
     */
    const INTEREST_UNIQUE = 'u';

    /**
     * @const
     */
    const INTEREST_SIMPLE = 's';

    /**
     * @const
     */
    const INTEREST_COMPOUND = 'c';

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
        'interest_type',
        'interest_rate',
        'member_id',
        'session_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline_date' => 'datetime:Y-m-d',
        ];
    }

    /**
     * @return Attribute
     */
    protected function principal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->principal_debt?->amount ?? 0,
        );
    }

    /**
     * @return Attribute
     */
    protected function interest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_debt?->amount ?? 0,
        );
    }

    /**
     * @return Attribute
     */
    protected function fixedInterest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_type === self::INTEREST_FIXED,
        );
    }

    /**
     * @return Attribute
     */
    protected function uniqueInterest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_type === self::INTEREST_UNIQUE,
        );
    }

    /**
     * @return Attribute
     */
    protected function simpleInterest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_type === self::INTEREST_SIMPLE,
        );
    }

    /**
     * @return Attribute
     */
    protected function compoundInterest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_type === self::INTEREST_COMPOUND,
        );
    }

    /**
     * @return Attribute
     */
    protected function recurrentInterest(): Attribute
    {
        // Interests that grows after each session.
        return Attribute::make(
            get: fn() => $this->interest_type === self::INTEREST_SIMPLE ||
                $this->interest_type === self::INTEREST_COMPOUND,
        );
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFixedInterest(Builder $query): Builder
    {
        return $query->where('interest_type', self::INTEREST_FIXED)
            ->orWhere('interest_type', self::INTEREST_UNIQUE);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function refunds()
    {
        return $this->hasManyThrough(Refund::class, Debt::class)
            ->select('refunds.*');
    }

    public function principal_debt()
    {
        // Read from the database
        return $this->hasOne(Debt::class)->where('type', Debt::TYPE_PRINCIPAL);
    }

    public function interest_debt()
    {
        // Read from the database
        return $this->hasOne(Debt::class)->where('type', Debt::TYPE_INTEREST);
    }

    public function deadline_session()
    {
        return $this->belongsTo(Session::class, 'deadline_session_id');
    }

    /**
     * @return Attribute
     */
    protected function pDebt(): Attribute
    {
        // Read from the "debts" collection
        return Attribute::make(
            get: fn() => $this->debts->first(fn($debt) => $debt->type === Debt::TYPE_PRINCIPAL),
        );
    }

    /**
     * @return Attribute
     */
    protected function iDebt(): Attribute
    {
        // Read from the "debts" collection
        return Attribute::make(
            get: fn() => $this->debts->first(fn($debt) => $debt->type === Debt::TYPE_INTEREST),
        );
    }

    /**
     * @return Attribute
     */
    protected function allRefunds(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->i_debt ? $this->p_debt->all_refunds :
                $this->p_debt->all_refunds->concat($this->i_debt->all_refunds),
        );
    }

    /**
     * @return Attribute
     */
    protected function deadline(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->deadline_session !== null ?
                trans('meeting.loan.deadline.session', [
                    'date' => $this->deadline_session->date('day_date', 'format_medium'),
                ]) :
                ($this->deadline_date !== null ?
                    trans('meeting.loan.deadline.date', [
                        'date' => $this->date('deadline_date', 'format_medium'),
                    ]) : '')
        );
    }

    /**
     * @return Attribute
     */
    protected function noDeadline(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->deadline_session && !$this->deadline_date,
        );
    }


    /**
     * @return Attribute
     */
    protected function deadlinePassed(): Attribute
    {
        return Attribute::make(
            get: function() {
                if($this->no_deadline)
                {
                    return false;
                }

                $date = $this->deadline_session !== null ?
                    $this->deadline_session->day_date : $this->deadline_date;
                return $date < Carbon::today();
            }
        );
    }
}
