<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Session extends Model
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
        'created_at',
        'updated_at',
        'deleted_at',
        'start_at',
        'end_at',
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
        return $this->start_at->format('l jS F Y');
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
            ->whereHas('remittance')
            ->groupBy('pools.id')
            ->select('pools.id', DB::raw('sum(pools.amount) as amount'));
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class)->orderBy('receivables.id', 'asc');
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

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function settlementAmounts()
    {
        return $this->hasMany(Settlement::class)
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->groupBy('bills.charge_id')
            ->select('bills.charge_id', DB::raw('sum(bills.amount) as amount'));
    }

    public function feeSettlementAmounts()
    {
        return $this->hasMany(Settlement::class)
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('charges', 'bills.charge_id', '=', 'charges.id')
            ->where('charges.type', '=', Charge::TYPE_FEE)
            ->groupBy('bills.charge_id')
            ->select('bills.charge_id', DB::raw('sum(bills.amount) as amount'));
    }

    public function fineSettlementAmounts()
    {
        return $this->hasMany(Settlement::class)
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('charges', 'bills.charge_id', '=', 'charges.id')
            ->where('charges.type', '=', Charge::TYPE_FINE)
            ->groupBy('bills.charge_id')
            ->select('bills.charge_id', DB::raw('sum(bills.amount) as amount'));
    }

    public function biddings()
    {
        return $this->hasMany(Bidding::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function disabledPools()
    {
        return $this->belongsToMany(Pool::class, 'pool_session_disabled');
    }

    public function enabled(Pool $pool)
    {
        return $this->disabledPools()->where('pool_id', $pool->id)->doesntExist();
    }

    public function disabled(Pool $pool)
    {
        return $this->disabledPools()->where('pool_id', $pool->id)->exists();
    }
}
