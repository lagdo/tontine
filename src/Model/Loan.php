<?php

namespace Siak\Tontine\Model;

class Loan extends Base
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
        'member_id',
        'session_id',
    ];

    public function getPrincipalAttribute()
    {
        return $this->principal_debt ? $this->principal_debt->amount : 0;
    }

    public function getInterestAttribute()
    {
        return $this->interest_debt ? $this->interest_debt->amount : 0;
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function remitment()
    {
        return $this->belongsTo(Remitment::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function refunds()
    {
        return $this->hasManyThrough(Refund::class, Debt::class);
    }

    public function principal_debt()
    {
        return $this->hasOne(Debt::class)->where('type', Debt::TYPE_PRINCIPAL);
    }

    public function interest_debt()
    {
        return $this->hasOne(Debt::class)->where('type', Debt::TYPE_INTEREST);
    }
}
