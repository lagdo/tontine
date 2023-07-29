<?php

namespace Siak\Tontine\Model;

class Receivable extends Base
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
        'session_id',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }
}
