<?php

namespace Siak\Tontine\Model;

use Database\Factories\FundFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Siak\Tontine\Model\Traits\HasCurrency;

use function array_unique;
use function array_values;
use function json_decode;
use function json_encode;

class Fund extends Model
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
        'session_ids',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return FundFactory::new();
    }

    public function getSessionIdsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSessionIdsAttribute(array $value)
    {
        $this->attributes['session_ids'] = json_encode(array_unique(array_values($value)));
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function sessions()
    {
        return Session::whereIn('id', $this->session_ids);
    }
}
