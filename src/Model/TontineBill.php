<?php

namespace Siak\Tontine\Model;

class TontineBill extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
