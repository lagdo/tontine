<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @const
     */
    const TYPE_PRINCIPAL = 'p';

    /**
     * @const
     */
    const TYPE_INTEREST = 'i';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
    ];

    public function getIsPrincipalAttribute()
    {
        return $this->type === self::TYPE_PRINCIPAL;
    }

    public function getIsInterestAttribute()
    {
        return $this->type === self::TYPE_INTEREST;
    }

    public function getAmountAttribute()
    {
        return $this->type === self::TYPE_PRINCIPAL ? $this->loan->amount : $this->loan->interest;
    }

    public function getTypeStrAttribute()
    {
        return $this->type === self::TYPE_PRINCIPAL ? 'principal' : 'interest';
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePrincipal(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_PRINCIPAL);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeInterest(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_INTEREST);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
