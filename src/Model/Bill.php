<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'amount',
        'issued_at',
        'deadline',
        'notes',
        'charge_id',
        'round_id',
        'session_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'due_at',
        'deadline',
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereExists(function($query) {
            $query->from('settlements')->where('bill_id', $this->id);
        });
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereNotExists(function($query) {
            $query->from('settlements')->where('bill_id', $this->id);
        });
    }
}
