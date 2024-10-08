<?php

namespace Siak\Tontine\Model;

class PartialRefund extends Base
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
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
