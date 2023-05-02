<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
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

    public function getOnlineAttribute()
    {
        return false;
    }
}
