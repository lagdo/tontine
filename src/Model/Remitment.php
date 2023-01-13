<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Remitment extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }
}
