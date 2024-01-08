<?php

namespace Siak\Tontine\Model;

class PoolCounter extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_pool_counters';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function pool()
    {
        return $this->belongsTo(Pool::class, 'id');
    }
}
