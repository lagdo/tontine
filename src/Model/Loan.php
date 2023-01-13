<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
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
        'amount_bid',
        'amount_paid',
    ];

    public function getAmountAttribute()
    {
        return Currency::format($this->amount_bid);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
