<?php

namespace Siak\Tontine\Model;

use Illuminate\Support\Facades\DB;

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
    protected $dates = [
        'start_at',
        'end_at',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'host',
        'disabledPools',
    ];

    public function getNotFirstAttribute()
    {
        return $this->round->sessions()->where('start_at', '<', $this->start_at)->exists();
    }

    public function getNotLastAttribute()
    {
        return $this->round->sessions()->where('start_at', '>', $this->start_at)->exists();
    }

    public function getAbbrevAttribute($value)
    {
        return $value ?? $this->start_at->format('M y');
    }

    public function getDateAttribute()
    {
        return $this->start_at->translatedFormat(trans('tontine.date.format'));
    }

    public function getTimesAttribute()
    {
        return $this->start_at->format('H:i') . ' - ' . $this->end_at->format('H:i');
    }

    public function getPendingAttribute()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getOpenedAttribute()
    {
        return $this->status === self::STATUS_OPENED;
    }

    public function getClosedAttribute()
    {
        return $this->status === self::STATUS_CLOSED;
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

    public function payableAmounts()
    {
        return $this->hasMany(Payable::class)
            ->join('subscriptions', 'subscriptions.id', '=', 'payables.subscription_id')
            ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
            ->whereHas('remitment')
            ->groupBy('pools.id')
            ->select('pools.id', DB::raw('sum(pools.amount) as amount'));
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function receivableAmounts()
    {
        return $this->hasMany(Receivable::class)
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->join('pools', 'subscriptions.pool_id', '=', 'pools.id')
            ->whereHas('deposit')
            ->groupBy('pools.id')
            ->select('pools.id', DB::raw('sum(pools.amount) as amount'));
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

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class);
    }

    public function disabledPools()
    {
        return $this->belongsToMany(Pool::class, 'pool_session_disabled');
    }

    public function enabled(Pool $pool)
    {
        return $this->disabledPools->find($pool->id) === null;
    }

    public function disabled(Pool $pool)
    {
        return $this->disabledPools->find($pool->id) !== null;
    }
}
