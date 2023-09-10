<?php

namespace Siak\Tontine\Model;

class Disbursement extends Base
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
        'comment',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
