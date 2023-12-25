<?php

namespace Siak\Tontine\Model;

class Round extends Base
{
    use Traits\HasProperty;

    /**
     * @const
     */
    const STATUS_PENDING = 0;

    /**
     * @const
     */
    const STATUS_OPENED = 1;

    /**
     * @const
     */
    const STATUS_CLOSED = 2;

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
        'status',
        'notes',
    ];

    public function getStartAtAttribute()
    {
        $startSession = $this->sessions()->orderBy('start_at')->first();
        return !$startSession ? null : $startSession->start_at;
    }

    public function getEndAtAttribute()
    {
        $endSession = $this->sessions()->orderByDesc('start_at')->first();
        return !$endSession ? null : $endSession->start_at;
    }

    public function getPendingAttribute()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getOpenedAttribute()
    {
        return $this->status === self::STATUS_OPENED;
    }

    public function getClosedAttribute()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function bills()
    {
        return $this->hasMany(RoundBill::class);
    }
}
