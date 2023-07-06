<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
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

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    public function getOnlineAttribute()
    {
        return false;
    }
}
