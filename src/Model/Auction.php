<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $amount
 * @property bool $paid
 * @property-read Remitment $remitment
 * @property-read Session $session
 * @method static Builder paid()
 * @method static Builder unpaid()
 */
class Auction extends Base
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
        'amount',
        'paid',
        'session_id',
        'remitment_id',
    ];

    public function remitment()
    {
        return $this->belongsTo(Remitment::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('paid', true);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('paid', false);
    }
}
