<?php

namespace Siak\Tontine\Model;

class PoolRound extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function start_session()
    {
        return $this->belongsTo(Session::class, 'start_session_id');
    }

    public function end_session()
    {
        return $this->belongsTo(Session::class, 'end_session_id');
    }
}
