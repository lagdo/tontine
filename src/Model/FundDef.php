<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class FundDef extends Base
{
    /**
     * @var int
     */
    public const TYPE_AUTO = 0;

    /**
     * @var int
     */
    public const TYPE_USER = 1;

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
        'type',
        'title',
        'notes',
        'active',
    ];

    public function typeAuto(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === self::TYPE_AUTO,
        );
    }

    public function typeUser(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === self::TYPE_USER,
        );
    }

    public function funds()
    {
        return $this->hasMany(Fund::class, 'def_id');
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeAuto(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_AUTO);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUser(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_USER);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
