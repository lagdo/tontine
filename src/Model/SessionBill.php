<?php

namespace Siak\Tontine\Model;

class SessionBill extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function session()
    {
        return $this->belongsTo(Session::class);
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
