<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

use function trans;

class ProfitTransfer extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_profit_transfers';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn() => trans($this->coef > 0 ?
                'meeting.labels.saving' : 'meeting.labels.settlement'),
        );
    }

    /**
     * @param  Builder  $query
     * @param  Fund  $fund
     *
     * @return Builder
     */
    public function scopeWhereFund(Builder $query, Fund $fund): Builder
    {
        return $query->where('fund_id', $fund->id);
    }

    /**
     * @param  Builder  $query
     * @param  Session  $session
     *
     * @return Builder
     */
    public function scopeWhereSession(Builder $query, Session $session): Builder
    {
        return $query->where('session_id', $session->id);
    }
}
