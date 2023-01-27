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
        'amount',
        'interest',
        'member_id',
        'session_id',
    ];

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

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function principal_refund()
    {
        return $this->hasOne(Refund::class)->where('type', Refund::TYPE_PRINCIPAL);
    }

    public function interest_refund()
    {
        return $this->hasOne(Refund::class)->where('type', Refund::TYPE_INTEREST);
    }
}
