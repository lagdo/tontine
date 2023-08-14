<?php

namespace Siak\Tontine\Model;

use Database\Factories\PoolFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Siak\Tontine\Model\Traits\HasCurrency;

use function trans;

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

    public function getTitleAttribute($value)
    {
        return $this->round !== null && $this->round->tontine->is_libre ?
            trans('tontine.pool.titles.free') : $value;
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
