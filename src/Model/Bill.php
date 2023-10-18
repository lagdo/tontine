<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

class Bill extends Base
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
        'charge',
        'amount',
        'lendable',
        'issued_at',
        'deadline',
        'notes',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'issued_at',
        'deadline',
    ];

    public function settlement()
    {
        return $this->hasOne(Settlement::class);
    }

    public function tontine_bill()
    {
        return $this->hasOne(TontineBill::class);
    }

    public function round_bill()
    {
        return $this->hasOne(RoundBill::class);
    }

    public function session_bill()
    {
        return $this->hasOne(SessionBill::class);
    }

    public function libre_bill()
    {
        return $this->hasOne(LibreBill::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('settlement');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereDoesntHave('settlement');
    }
}
