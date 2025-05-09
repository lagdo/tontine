<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Debt extends Base
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
        'amount',
    ];

    public function isPrincipal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === self::TYPE_PRINCIPAL,
        );
    }

    public function isInterest(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === self::TYPE_INTEREST,
        );
    }

    public function typeStr(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === self::TYPE_PRINCIPAL ? 'principal' : 'interest',
        );
    }

    /**
     * @return Attribute
     */
    protected function dueAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->amount - $this->partial_refunds->sum('amount'),
        );
    }

    /**
     * @return Attribute
     */
    protected function allRefunds(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->refund ? $this->partial_refunds :
                $this->partial_refunds->push($this->refund),
        );
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

    public function partial_refunds()
    {
        return $this->hasMany(PartialRefund::class);
    }

    /**
     * Will be used to get the unique partial refund for a given session
     */
    public function partial_refund()
    {
        // We use latest() instead of latestOfMany() because it is simpler to
        // add clauses with extra parameters to the subquery.
        return $this->hasOne(PartialRefund::class)->latest('id');
    }
}
