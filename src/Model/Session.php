<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Session extends Base
{
    use Traits\DateFormatter;

    /**
     * @const
     */
    const STATUS_PENDING = 0;

    /**
     * @const
     */
    const STATUS_OPENED = 1;

    /**
     * @const
     */
    const STATUS_CLOSED = 2;

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
        'title',
        'abbrev',
        'agenda',
        'report',
        'status',
        'notes',
        'venue',
        'day_date',
        'start_time',
        'end_time',
        'host_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day_date' => 'datetime:Y-m-d',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function notFirst(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->round->sessions()->where('day_date', '<', $this->day_date)->exists(),
        );
    }

    public function notLast(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->round->sessions()->where('day_date', '>', $this->day_date)->exists(),
        );
    }

    public function abbrev(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? $this->day_date->format('M y'),
        );
    }

    public function times(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i'),
        );
    }

    public function pending(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_PENDING,
        );
    }

    public function opened(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_OPENED,
        );
    }

    public function closed(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_CLOSED,
        );
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function host()
    {
        return $this->belongsTo(Member::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class)->orderBy('payables.id', 'asc');
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function session_bills()
    {
        return $this->hasMany(SessionBill::class);
    }

    public function libre_bills()
    {
        return $this->hasMany(LibreBill::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function partial_refunds()
    {
        return $this->hasMany(PartialRefund::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function outflows()
    {
        return $this->hasMany(Outflow::class);
    }

    public function funds()
    {
        return $this->belongsToMany(Fund::class, 'v_fund_session');
    }

    public function pools()
    {
        return $this->belongsToMany(Pool::class, 'v_pool_session');
    }

    public function disabled_pools()
    {
        return $this->belongsToMany(Pool::class, 'pool_session_disabled');
    }

    public function absents()
    {
        return $this->belongsToMany(Member::class, 'absences');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('sessions.status', '!=', self::STATUS_PENDING);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->where('status', '=', self::STATUS_OPENED);
    }
}
