<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
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

    public function getOnlineAttribute()
    {
        return false;
    }
}
