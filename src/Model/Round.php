<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'notes',
        'start_at',
        'end_at',
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
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime:Y-m-d',
        'end_at' => 'datetime:Y-m-d',
    ];

    public function getStartAttribute()
    {
        return $this->start_at->toFormattedDateString();
    }

    public function getEndAttribute()
    {
        return $this->end_at->toFormattedDateString();
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function pools()
    {
        return $this->hasMany(Pool::class)->orderBy('id', 'asc');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class)->orderBy('sessions.start_at', 'asc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
