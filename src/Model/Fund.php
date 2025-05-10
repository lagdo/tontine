<?php

namespace Siak\Tontine\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function trans;

class Fund extends Base
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
            'start_date' => 'datetime:Y-m-d',
            'end_date' => 'datetime:Y-m-d',
            'interest_date' => 'datetime:Y-m-d',
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
     * Get the start amount.
     *
     * @return Attribute
     */
    public function startAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->options['amount']['start'] ?? 0,
        );
    }

    /**
     * Get the end amount.
     *
     * @return Attribute
     */
    public function endAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->options['amount']['end'] ?? 0,
        );
    }

    /**
     * Get the profit amount.
     *
     * @return Attribute
     */
    protected function profitAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->options['amount']['profit'] ?? 0,
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
                'v.end_date',
                'v.start_date',
                'v.interest_date',
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
        return $query->where('v.end_date', '>=', $startDate)
            ->where('v.start_date', '<=', $endDate);
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
        return $this->filterOnDates($query, $session->day_date, $session->day_date);
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
