<?php

namespace Siak\Tontine\Model;

class Saving extends Base
{
    /**
     * This is a custom attribute used to calculate profit distribution
     *
     * @var bool
     */
    public $duration = 0;

    /**
     * This is a custom attribute used to calculate profit distribution
     *
     * @var bool
     */
    public $distribution = 0;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
