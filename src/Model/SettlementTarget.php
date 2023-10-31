<?php

namespace Siak\Tontine\Model;

class SettlementTarget extends Base
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
        'global',
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function deadline()
    {
        return $this->belongsTo(Session::class);
    }
}
