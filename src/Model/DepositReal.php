<?php

namespace Siak\Tontine\Model;

class DepositReal extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deposits';

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

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }
}
