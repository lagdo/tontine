<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Pool extends Base
{
    use Traits\DateFormatter;

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
        'round_id',
        'start_sid',
        'end_sid',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'def',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Scope for pool sessions. Always applied by default.
        static::addGlobalScope('sessions', function (Builder $query) {
            $query->addSelect([
                'pools.*',
                'vp.end_date',
                'vp.start_date',
                'vp.sessions_count',
            ])->join(DB::raw('v_pools as vp'), 'vp.pool_id', '=', 'pools.id');
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime:Y-m-d',
            'end_date' => 'datetime:Y-m-d',
        ];
    }

    public function title(): Attribute
    {
        return Attribute::make(get: fn() => $this->def->title);
    }

    public function amount(): Attribute
    {
        return Attribute::make(get: fn() => $this->def->amount);
    }

    public function notes(): Attribute
    {
        return Attribute::make(get: fn() => $this->def->notes);
    }

    public function depositFixed(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->def->properties['deposit']['fixed'] ?? true) === true,
        );
    }

    public function depositLendable(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->def->properties['deposit']['lendable'] ?? false) === true,
        );
    }

    public function remitPlanned(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->def->properties['remit']['planned'] ?? true) === true,
        );
    }

    public function remitAuction(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->def->properties['remit']['auction'] ?? false) === true,
        );
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
        return $query->where('vp.end_date', '>=', $startDate)
            ->where('vp.start_date', '<=', $endDate);
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
        $query->whereHas('def', fn($q) => $q->where('guild_id', $round->guild_id));
        return $this->filterOnDates($query, $round->start_date, $round->end_date);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRemitPlanned(Builder $query): Builder
    {
        return $query->whereHas('def', fn($q) => $q->where('properties->remit->planned', true));
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function receivables()
    {
        return $this->hasManyThrough(Receivable::class, Subscription::class);
    }

    public function def()
    {
        return $this->belongsTo(PoolDef::class, 'def_id');
    }

    public function start()
    {
        return $this->belongsTo(Session::class, 'start_sid');
    }

    public function end()
    {
        return $this->belongsTo(Session::class, 'end_sid');
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'v_pool_session');
    }

    public function disabled_sessions()
    {
        // Filter on the sessions in the pool timespan.
        return $this->belongsToMany(Session::class, 'v_pool_session_disabled');
    }

    public function fund()
    {
        return $this->hasOne(Fund::class);
    }
}
