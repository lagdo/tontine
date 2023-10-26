<?php

namespace Siak\Tontine\Model;

class Settlement extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function getEditableAttribute()
    {
        return true;
    }
}
