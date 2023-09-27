<?php

namespace Siak\Tontine\Model;

class Remitment extends Base
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
        'amount',
    ];

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }
}
