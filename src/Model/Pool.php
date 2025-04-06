<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Database\Factories\PoolFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Traits\HasCurrency;

use function trans;

/**
 * @property string $title
 * @property int $amount
 * @property string $notes
 * @property array $properties
 * @property-read Carbon $start_at
 * @property-read Carbon $end_at
 * @property-read string $start_date
 * @property-read string $end_date
 * @property-read bool $deposit_fixed
 * @property-read bool $deposit_lendable
 * @property-read bool $remit_planned
 * @property-read bool $remit_auction
 * @property-read bool $remit_payable
 * @property-read Collection $subscriptions
 * @property-read Collection $sessions
 * @property-read Collection $disabled_sessions
 * @property-read Round $round
 * @property-read PoolRound $pool_round
 * @property-read Guild $guild
 * @method static Builder ofSession(Session $session)
 * @method static Builder ofRound(Round $round)
 */
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
            $query
                ->addSelect(['pools.*', 'v.end_at', 'v.start_at', 'v.guild_id'])
                ->join(DB::raw('v_pools as v'), 'v.id', '=', 'pools.id');
        });
    }

    /**
     * @param Builder $query
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return Builder
     */
    private function filterOnDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query
            ->whereDate('v.end_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('v.start_at', '<=', $endDate->format('Y-m-d'));
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
        return $query->where(function($query) use($round) {
            $query->where('pools.round_id', $round->id)
                ->when($round->start_at !== null && $round->end_at !== null,
                    function($query) use($round) {
                        $query->orWhere(function($query) use($round) {
                            // Take the other pools of the guild with overlapped sessions.
                            $query->where('v.guild_id', $round->guild_id);
                            $this->filterOnDates($query, $round->start_at, $round->end_at);
                        });
                    });
        });
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
        $query->where('v.guild_id', $session->round->guild_id);
        return $this->filterOnDates($query, $session->start_at, $session->start_at)
            // Also filter on enabled sessions.
            ->whereDoesntHave('disabled_sessions',
                fn($q) => $q->where('sessions.id', $session->id));
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRemitPlanned(Builder $query): Builder
    {
        return $query->where('properties->remit->planned', true);
    }

    public function startDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_at->translatedFormat(trans('tontine.date.format')),
        );
    }

    public function endDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->end_at->translatedFormat(trans('tontine.date.format')),
        );
    }

    public function depositFixed(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['deposit']['fixed'] ?? true) === true,
        );
    }

    public function depositLendable(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['deposit']['lendable'] ?? false) === true,
        );
    }

    public function remitPlanned(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['remit']['planned'] ?? true) === true,
        );
    }

    public function remitAuction(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['remit']['auction'] ?? false) === true,
        );
    }

    public function remitPayable(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->remit_planned && !$this->remit_auction,
        );
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function pool_round()
    {
        return $this->hasOne(PoolRound::class);
    }

    public function counter()
    {
        return $this->hasOne(PoolCounter::class, 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function disabled_sessions()
    {
        return $this->belongsToMany(Session::class, 'pool_session_disabled');
    }
}
