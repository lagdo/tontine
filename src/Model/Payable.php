<?php

namespace Siak\Tontine\Model;

class Payable extends Base
{
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
        'notes',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function remitment()
    {
        return $this->hasOne(Remitment::class);
    }
}
