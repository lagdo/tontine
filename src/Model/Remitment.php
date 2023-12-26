<?php

namespace Siak\Tontine\Model;

class Remitment extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function auction()
    {
        return $this->hasOne(Auction::class);
    }
}
