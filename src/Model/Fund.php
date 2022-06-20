<?php

namespace Siak\Tontine\Model;

use Database\Factories\FundFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Traits\HasCurrency;

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
        return $this->belongsToMany(Session::class, 'fund_session_disabled');
    }

    public function sessions()
    {
        return Session::whereNotIn('id',
            DB::table('fund_session_disabled')->where('fund_id', $this->id)->pluck('session_id'));
    }
}
