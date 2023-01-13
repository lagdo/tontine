<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
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

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
