<?php

namespace Siak\Tontine\Model;

class Refund extends Base
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

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function getEditableAttribute()
    {
        return true;
    }
}
