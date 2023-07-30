<?php

namespace Siak\Tontine\Model;

class Subscription extends Base
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
        'title',
    ];

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payable()
    {
        return $this->hasOne(Payable::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }
}
