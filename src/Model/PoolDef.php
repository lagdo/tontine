<?php

namespace Siak\Tontine\Model;

use Database\Factories\PoolFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoolDef extends Base
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
        'amount',
        'notes',
        'properties',
        'active',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'properties' => '{}',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return PoolFactory::new();
    }

    public function depositFixed(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['deposit']['fixed'] ?? true) === true,
        );
    }

    public function depositLendable(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['deposit']['lendable'] ?? false) === true,
        );
    }

    public function remitPlanned(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['remit']['planned'] ?? true) === true,
        );
    }

    public function remitAuction(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->properties['remit']['auction'] ?? false) === true,
        );
    }

    public function pools()
    {
        return $this->hasMany(Pool::class, 'def_id');
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeDepositLendable(Builder $query): Builder
    {
        return $query->where('properties->deposit->lendable', true);
    }
}
