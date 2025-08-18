<?php

namespace Siak\Tontine\Model;

class Deposit extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_deposits';

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
}
