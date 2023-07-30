<?php

namespace Siak\Tontine\Model;

use Database\Factories\TontineFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tontine extends Base
{
    use HasFactory;
    use Traits\HasProperty;

    /**
     * @const
     */
    const TYPE_LIBRE = 'l';

    /**
     * @const
     */
    const TYPE_MUTUAL = 'm';

    /**
     * @const
     */
    const TYPE_FINANCIAL = 'f';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'shortname',
        'biography',
        'email',
        'phone',
        'address',
        'city',
        'website',
        'country_code',
        'currency_code',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return TontineFactory::new();
    }

    public function getIsLibreAttribute()
    {
        return $this->type === self::TYPE_LIBRE;
    }

    public function getIsMutualAttribute()
    {
        return $this->type === self::TYPE_MUTUAL;
    }

    public function getIsFinancialAttribute()
    {
        return $this->type === self::TYPE_FINANCIAL;
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeLibre(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_LIBRE);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeMutual(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_MUTUAL);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFinancial(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FINANCIAL);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class)->orderBy('name', 'asc');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class)->orderBy('rounds.id', 'desc');
    }

    public function charges()
    {
        return $this->hasMany(Charge::class)->orderBy('charges.id', 'asc');
    }
}
