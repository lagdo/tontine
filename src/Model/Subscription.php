<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
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

    public function fund()
    {
        return $this->belongsTo(Fund::class);
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
