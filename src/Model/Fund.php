<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function trans;

class Fund extends Base
{
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
        'options',
        'def_id',
        'round_id',
        'start_sid',
        'end_sid',
        'interest_sid',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'options' => '{}',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'interest_at' => 'datetime',
        ];
    }

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'def',
    ];

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn() => match(true) {
                $this->def->type_user => $this->def->title,
                $this->pool !== null => $this->pool->title,
                default => trans('tontine.fund.labels.default'),
            },
        );
    }

    public function notes(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->def->notes,
        );
    }

    /**
     * Get the profit amount.
     *
     * @return Attribute
     */
    protected function profit(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->options['profit']['amount'] ?? 0,
            set: function(int $amount) {
                $options = $this->options;
                $options['profit']['amount'] = $amount;
                // Return the fields to be set on the model.
                return ['options' => json_encode($options)];
            },
        );
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Scope for fund sessions. Always applied by default.
        static::addGlobalScope('sessions', function (Builder $query) {
            $query->addSelect([
                'funds.*',
                'v.end_at',
                'v.start_at',
                'v.interest_at',
                'v.sessions_count',
            ])->join(DB::raw('v_funds as v'), 'v.fund_id', '=', 'funds.id');
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
        return $query->where('v.end_at', '>=', $startDate)
            ->where('v.start_at', '<=', $endDate);
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
        return $this->filterOnDates($query, $round->start_at, $round->end_at);
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
        $query->whereHas('def', fn($q) => $q->where('guild_id', $session->round->guild_id));
        return $this->filterOnDates($query, $session->start_at, $session->start_at);
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

    public function interestDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->interest_at->translatedFormat(trans('tontine.date.format')),
        );
    }

    public function def()
    {
        return $this->belongsTo(FundDef::class, 'def_id');
    }

    public function start()
    {
        return $this->belongsTo(Session::class, 'start_sid');
    }

    public function end()
    {
        return $this->belongsTo(Session::class, 'end_sid');
    }

    public function interest()
    {
        return $this->belongsTo(Session::class, 'interest_sid');
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'v_fund_session');
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeReal(Builder $query): Builder
    {
        return $query->whereNull('pool_id');
    }
}
