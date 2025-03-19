<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

use function trans;

class Session extends Base
{
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
        'start_at',
        'end_at',
        'host_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'host',
        'disabled_pools',
    ];

    public function notFirst(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->round->sessions()->where('start_at', '<', $this->start_at)->exists(),
        );
    }

    public function notLast(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->round->sessions()->where('start_at', '>', $this->start_at)->exists(),
        );
    }

    public function abbrev(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? $this->start_at->format('M y'),
        );
    }

    public function date(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_at->translatedFormat(trans('tontine.date.format')),
        );
    }

    public function times(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_at->format('H:i') . ' - ' . $this->end_at->format('H:i'),
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

    public function closings()
    {
        return $this->hasMany(Closing::class);
    }

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class);
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
     * @param  Pool $pool
     *
     * @return Builder
     */
    public function scopeEnabled(Builder $query, Pool $pool): Builder
    {
        return $query->whereDoesntHave('disabled_pools',
            fn($q) => $q->where('pools.id', $pool->id));
    }

    /**
     * @param  Builder  $query
     * @param  Pool $pool
     *
     * @return Builder
     */
    public function scopeDisabled(Builder $query, Pool $pool): Builder
    {
        return $query->whereHas('disabled_pools',
            fn($q) => $q->where('pools.id', $pool->id));
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

    /**
     * @param  Builder  $query
     * @param  Pool $pool
     *
     * @return Builder
     */
    public function scopeOfPool(Builder $query, Pool $pool): Builder
    {
        return !$pool->pool_round ?
            $query->where('round_id', $pool->round_id) :
            $query->whereDate('start_at', '<=', $pool->end_at->format('Y-m-d'))
                ->whereDate('start_at', '>=', $pool->start_at->format('Y-m-d'));
    }
}
