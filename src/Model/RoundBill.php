<?php

namespace Siak\Tontine\Model;

class RoundBill extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
