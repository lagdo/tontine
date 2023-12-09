<?php

namespace Siak\Tontine\Model;

use Database\Factories\PoolFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Siak\Tontine\Model\Traits\HasCurrency;

class Pool extends Base
{
    use HasFactory;
    use HasCurrency;

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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
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
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return PoolFactory::new();
    }

    public function getDepositFixedAttribute()
    {
        return $this->properties['deposit']['fixed'] === true;
    }

    public function getRemitFixedAttribute()
    {
        return $this->properties['remit']['fixed'] === true;
    }

    public function getRemitPlannedAttribute()
    {
        return $this->properties['remit']['planned'] === true;
    }

    public function getRemitAuctionAttribute()
    {
        return $this->properties['remit']['auction'] === true;
    }

    public function getRemitLendableAttribute()
    {
        return $this->properties['remit']['lendable'] === true;
    }

    public function getRemitPayableAttribute()
    {
        return $this->remit_fixed && $this->remit_planned && !$this->remit_auction;
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function disabledSessions()
    {
        return $this->belongsToMany(Session::class, 'pool_session_disabled');
    }
}
