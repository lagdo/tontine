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

    public function debts()
    {
        return $this->hasMany(Debt::class);
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
