<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Database\Factories\PoolFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Traits\HasCurrency;

use function trans;

class Pool extends Base
{
    use HasFactory;
    use HasCurrency;

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
        'amount',
        'notes',
        'properties',
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'properties' => '{}',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'pool_round',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return PoolFactory::new();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('dates', function (Builder $query) {
            $query->select('pools.*', 'v.end_at', 'v.start_at', 'v.tontine_id')
                ->join(DB::raw('v_pools as v'), 'v.id', '=', 'pools.id');
        });
    }

    /**
     * @param Builder $query
     * @param Round $round
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return Builder
     */
    private function filterOnRoundOrDates(Builder $query, Round $round,
        Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function(Builder $query) use($round, $startDate, $endDate) {
                $query->orWhere('pools.round_id', $round->id)
                    ->orWhere(function(Builder $query) use($round, $startDate, $endDate) {
                        $query->where('v.tontine_id', $round->tontine_id)
                            ->whereDate('v.end_at', '>=', $startDate->format('Y-m-d'))
                            ->whereDate('v.start_at', '<=', $endDate->format('Y-m-d'));
                    });
            });
    }

    /**
     * Scope to active pools for a given round.
     *
     * @param Builder $query
     * @param Round $round
     *
     * @return Builder
     */
    public function scopeOfRound(Builder $query, Round $round): Builder
    {
        $startDate = $round->start_at;
        $endDate = $round->end_at;
        return $this->filterOnRoundOrDates($query, $round, $startDate, $endDate);
    }

    /**
     * Scope to active pools for a given session.
     *
     * @param Builder $query
     * @param Session $session
     *
     * @return Builder
     */
    public function scopeOfSession(Builder $query, Session $session): Builder
    {
        $date = $session->start_at;
        return $this->filterOnRoundOrDates($query, $session->round, $date, $date);
    }

    public function getStartDateAttribute()
    {
        return $this->start_at->translatedFormat(trans('tontine.date.format'));
    }

    public function getEndDateAttribute()
    {
        return $this->end_at->translatedFormat(trans('tontine.date.format'));
    }

    public function getDepositFixedAttribute()
    {
        return $this->properties['deposit']['fixed'] === true;
    }

    public function getRemitPlannedAttribute()
    {
        return $this->properties['remit']['planned'] === true;
    }

    public function getRemitAuctionAttribute()
    {
        return $this->properties['remit']['auction'] === true;
    }

    public function getRemitLendableAttribute()
    {
        return $this->properties['remit']['lendable'] === true;
    }

    public function getRemitPayableAttribute()
    {
        return $this->remit_planned && !$this->remit_auction;
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function pool_round()
    {
        return $this->hasOne(PoolRound::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function disabledSessions()
    {
        return $this->belongsToMany(Session::class, 'pool_session_disabled');
    }

    public function tontine()
    {
        // The "tontine_id" field is added to the table by the join
        // with the v_pools view. So we can now add this relationship.
        return $this->belongsTo(Tontine::class);
    }

    public function sessions()
    {
        if(!$this->pool_round)
        {
            return $this->round->sessions();
        }
        return $this->tontine->sessions()
            ->whereDate('start_at', '>=', $this->start_at->format('Y-m-d'))
            ->whereDate('start_at', '<=', $this->end_at->format('Y-m-d'));
    }

    public function enabledSessions()
    {
        return $this->sessions()->whereDoesntHave('disabledPools',
            fn(Builder $query) => $query->where('pools.id', $this->id));
    }
}
